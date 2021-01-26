<?php

use CParser_HtmlParser_Tokenizer_State as State;
use CParser_HtmlParser_Tokenizer_Special as Special;
use CParser_HtmlParser_Tokenizer_Helper as Helper;
use CParser_HtmlParser_Tokenizer_Entity as Entity;

class CParser_HtmlParser_Tokenizer {
    /**
     * The current state the tokenizer is in.
     */
    protected $state = State::Text;

    /**
     * The read buffer.
     */
    protected $buffer = '';

    /**
     * The beginning of the section that is currently being read.
     */
    protected $sectionStart = 0;

    /**
     * The index within the buffer that we are currently looking at.
     */
    protected $index = 0;

    /**
     * Data that has already been processed will be removed from the buffer occasionally.
     * `_bufferOffset` keeps track of how many characters have been removed, to make sure position information is accurate.
     */
    protected $bufferOffset = 0;

    /**
     * Some behavior, eg. when decoding entities, is done while we are in another state. This keeps track of the other state type.
     */
    protected $baseState = State::Text;

    /**
     * For special parsing behavior inside of script and style tags.
     */
    protected $special = Special::None;

    /**
     * Indicates whether the tokenizer has been paused.
     */
    protected $running = true;

    /**
     * Indicates whether the tokenizer has finished running / `.end` has been called.
     */
    protected $ended = false;

    /**
     * @var CParser_HtmlParser_TokenizerCallbackInterface
     */
    protected $callbacks;

    /**
     * @var boolean
     */
    protected $xmlMode = false;

    /**
     * @var boolean
     */
    protected $decodeEntities = false;

    public function __construct($options = [], CParser_HtmlParser_TokenizerCallbackInterface $callbacks = null) {
        $this->xmlMode = carr::get($options, 'xmlMode', false);
        $this->decodeEntities = carr::get($options, 'decodeEntities', false);
        $this->callbacks = $callbacks;
    }

    public function reset() {
        $this->state = State::Text;
        $this->buffer = '';
        $this->sectionStart = 0;
        $this->index = 0;
        $this->bufferOffset = 0;
        $this->baseState = State::Text;
        $this->special = Special::None;
        $this->running = true;
        $this->ended = false;
    }

    protected function stateText($c) {
        if ($c === '<') {
            if ($this->index > $this->sectionStart) {
                $this->callbacks->ontext($this->getSection());
            }
            $this->state = State::BeforeTagName;
            $this->sectionStart = $this->index;
        } elseif ($this->decodeEntities
            && $this->special === Special::None
            && $c === '&'
        ) {
            if ($this->index > $this->sectionStart) {
                $this->callbacks->ontext($this->getSection());
            }
            $this->baseState = State::Text;
            $this->state = State::BeforeEntity;
            $this->sectionStart = $this->index;
        }
    }

    protected function stateBeforeTagName($c) {
        if ($c === '/') {
            $this->state = State::BeforeClosingTagName;
        } elseif ($c === '<') {
            $this->callbacks->ontext($this->getSection());
            $this->sectionStart = $this->index;
        } elseif ($c === '>'
            || $this->special !== Special::None
            || Helper::isWhiteSpace($c)
        ) {
            $this->state = State::Text;
        } elseif ($c === '!') {
            $this->state = State::BeforeDeclaration;
            $this->sectionStart = $this->index + 1;
        } elseif ($c === '?') {
            $this->state = State::InProcessingInstruction;
            $this->sectionStart = $this->index + 1;
        } else {
            $this->state = !$this->xmlMode && ($c === 's' || $c === 'S') ? State::BeforeSpecial : State::InTagName;
            $this->sectionStart = $this->index;
        }
    }

    protected function stateInTagName($c) {
        if ($c === '/' || $c === '>' || Helper::isWhiteSpace($c)) {
            $this->emitToken('onopentagname');
            $this->state = State::BeforeAttributeName;
            $this->index--;
        }
    }

    protected function stateBeforeCloseingTagName($c) {
        if (Helper::isWhiteSpace($c)) {
            // ignore
        } elseif ($c === '>') {
            $this->state = State::Text;
        } elseif ($this->special !== Special::None) {
            if ($c === 's' || $c === 'S') {
                $this->state = State::BeforeSpecialEnd;
            } else {
                $this->state = State::Text;
                $this->index--;
            }
        } else {
            $this->state = State::InClosingTagName;
            $this->sectionStart = $this->index;
        }
    }

    protected function stateInCloseingTagName($c) {
        if ($c === '>' || Helper::isWhiteSpace($c)) {
            $this->emitToken('onclosetag');
            $this->state = State::AfterClosingTagName;
            $this->index--;
        }
    }

    protected function stateAfterCloseingTagName($c) {
        //skip everything until ">"
        if ($c === '>') {
            $this->state = State::Text;
            $this->sectionStart = $this->index + 1;
        }
    }

    protected function stateBeforeAttributeName($c) {
        if ($c === '>') {
            $this->callbacks->onopentagend();
            $this->state = State::Text;
            $this->sectionStart = $this->index + 1;
        } elseif ($c === '/') {
            $this->state = State::InSelfClosingTag;
        } elseif (!Helper::isWhiteSpace($c)) {
            $this->state = State::InAttributeName;
            $this->sectionStart = $this->index;
        }
    }

    protected function stateInSelfClosingTag($c) {
        if ($c === '>') {
            $this->callbacks->onselfclosingtag();
            $this->state = State::Text;
            $this->sectionStart = $this->index + 1;
        } elseif (!Helper::isWhiteSpace($c)) {
            $this->state = State::BeforeAttributeName;
            $this->index--;
        }
    }

    protected function stateInAttributeName($c) {
        if ($c === '=' || $c === '/' || $c === '>' || Helper::isWhiteSpace($c)) {
            $this->callbacks->onattribname($this->getSection());
            $this->sectionStart = -1;
            $this->state = State::AfterAttributeName;
            $this->index--;
        }
    }

    protected function stateAfterAttributeName($c) {
        if ($c === '=') {
            $this->state = State::BeforeAttributeValue;
        } elseif ($c === '/' || $c === '>') {
            $this->callbacks->onattribend();
            $this->state = State::BeforeAttributeName;
            $this->index--;
        } elseif (!Helper::isWhiteSpace($c)) {
            $this->callbacks->onattribend();
            $this->state = State::InAttributeName;
            $this->sectionStart = $this->index;
        }
    }

    protected function stateBeforeAttributeValue($c) {
        if ($c === '"') {
            $this->state = State::InAttributeValueDq;
            $this->sectionStart = $this->index + 1;
        } elseif ($c === "'") {
            $this->state = State::InAttributeValueSq;
            $this->sectionStart = $this->index + 1;
        } elseif (!Helper::isWhiteSpace($c)) {
            $this->state = State::InAttributeValueNq;
            $this->sectionStart = $this->index;
            $this->index--; //reconsume token
        }
    }

    protected function stateInAttributeValueDoubleQuotes($c) {
        if ($c === '"') {
            $this->emitToken('onattribdata');
            $this->callbacks->onattribend();
            $this->state = State::BeforeAttributeName;
        } elseif ($this->decodeEntities && $c === '&') {
            $this->emitToken('onattribdata');
            $this->baseState = $this->state;
            $this->state = State::BeforeEntity;
            $this->sectionStart = $this->index;
        }
    }

    protected function stateInAttributeValueSingleQuotes($c) {
        if ($c === "'") {
            $this->emitToken('onattribdata');
            $this->callbacks->onattribend();
            $this->state = State::BeforeAttributeName;
        } elseif ($this->decodeEntities && $c === '&') {
            $this->emitToken('onattribdata');
            $this->baseState = $this->state;
            $this->state = State::BeforeEntity;
            $this->sectionStart = $this->index;
        }
    }

    protected function stateInAttributeValueNoQuotes($c) {
        if (Helper::isWhiteSpace($c) || $c === '>') {
            $this->emitToken('onattribdata');
            $this->callbacks->onattribend();
            $this->state = State::BeforeAttributeName;
            $this->index--;
        } elseif ($this->decodeEntities && $c === '&') {
            $this->emitToken('onattribdata');
            $this->baseState = $this->state;
            $this->state = State::BeforeEntity;
            $this->sectionStart = $this->index;
        }
    }

    protected function stateBeforeDeclaration($c) {
        $this->state = $c === '[' ? State::BeforeCdata1 : ($c === '-' ? State::BeforeComment : State::InDeclaration);
    }

    protected function stateInDeclaration($c) {
        if ($c === '>') {
            $this->callbacks->ondeclaration($this->getSection());
            $this->state = State::Text;
            $this->sectionStart = $this->index + 1;
        }
    }

    protected function stateInProcessingInstruction($c) {
        if ($c === '>') {
            $this->callbacks->onprocessinginstruction($this->getSection());
            $this->state = State::Text;
            $this->sectionStart = $this->index + 1;
        }
    }

    protected function stateBeforeComment($c) {
        if ($c === '-') {
            $this->state = State::InComment;
            $this->sectionStart = $this->index + 1;
        } else {
            $this->state = State::InDeclaration;
        }
    }

    protected function stateInComment($c) {
        if ($c === '-') {
            $this->state = State::AfterComment1;
        }
    }

    protected function stateAfterComment1($c) {
        if ($c === '-') {
            $this->state = State::AfterComment2;
        } else {
            $this->state = State::InComment;
        }
    }

    protected function stateAfterComment2($c) {
        if ($c === '>') {
            //remove 2 trailing chars
            $this->callbacks->oncomment(
                $this->buffer . substr($this->sectionStart, $this->index - 2)
            );
            $this->state = State::Text;
            $this->sectionStart = $this->index + 1;
        } elseif ($c !== '-') {
            $this->state = State::InComment;
        }
        // else: stay in AFTER_COMMENT_2 (`--->`)
    }

    protected function stateBeforeCdata6($c) {
        if ($c === '[') {
            $this->state = State::InCdata;
            $this->sectionStart = $this->index + 1;
        } else {
            $this->state = State::InDeclaration;
            $this->index--;
        }
    }

    protected function stateInCdata($c) {
        if ($c === ']') {
            $this->state = State::AfterCdata1;
        }
    }

    protected function stateAfterCdata1($c) {
        if ($c === ']') {
            $this->state = State::AfterCdata2;
        } else {
            $this->state = State::InCdata;
        }
    }

    protected function stateAfterCdata2($c) {
        if ($c === '>') {
            //remove 2 trailing chars
            $this->callbacks->oncdata(
                substr($this->buffer, $this->sectionStart, $this->index - 2 - $this->sectionStart)
            );
            $this->state = State::Text;
            $this->sectionStart = $this->index + 1;
        } elseif ($c !== ']') {
            $this->state = State::InCdata;
        }
        //else: stay in AFTER_CDATA_2 (`]]]>`)
    }

    protected function stateBeforeSpecial($c) {
        if ($c === 'c' || $c === 'C') {
            $this->state = State::BeforeScript1;
        } elseif ($c === 't' || $c === 'T') {
            $this->state = State::BeforeStyle1;
        } else {
            $this->state = State::InTagName;
            $this->index--; //consume the token again
        }
    }

    protected function stateBeforeSpecialEnd($c) {
        if ($this->special === Special::Script && ($c === 'c' || $c === 'C')) {
            $this->state = State::AfterScript1;
        } elseif ($this->special === Special::Style
            && ($c === 't' || $c === 'T')
        ) {
            $this->state = State::AfterStyle1;
        } else {
            $this->state = State::Text;
        }
    }

    protected function stateBeforeScript5($c) {
        if ($c === '/' || $c === '>' || Helper::isWhiteSpace($c)) {
            $this->special = Special::Script;
        }
        $this->state = State::InTagName;
        $this->index--; //consume the token again
    }

    protected function stateAfterScript5($c) {
        if ($c === '>' || Helper::isWhiteSpace($c)) {
            $this->special = Special::None;
            $this->state = State::InClosingTagName;
            $this->sectionStart = $this->index - 6;
            $this->index--; //reconsume the token
        } else {
            $this->state = State::Text;
        }
    }

    protected function stateBeforeStyle4($c) {
        if ($c === '/' || $c === '>' || Helper::isWhiteSpace($c)) {
            $this->special = Special::Style;
        }
        $this->state = State::InTagName;
        $this->index--; //consume the token again
    }

    protected function stateAfterStyle4($c) {
        if ($c === '>' || Helper::isWhiteSpace($c)) {
            $this->special = Special::None;
            $this->state = State::InClosingTagName;
            $this->sectionStart = $this->index - 5;
            $this->index--; //reconsume the token
        } else {
            $this->state = State::Text;
        }
    }

    /**
     * For entities terminated with a semicolon
     *
     * @return void
     */
    protected function parseNamedEntityStrict() {
        //offset = 1
        if ($this->sectionStart + 1 < $this->index) {
            $entity = substr(
                $this->buffer,
                $this->sectionStart + 1,
                $this->index - ($this->sectionStart + 1)
            );
            $map = $this->xmlMode ? Entity::$xmlMap : Entity::$entityMap;
            if (isset($map[$entity])) {
                // @ts-ignore
                $this->emitPartial($map[$entity]);
                $this->sectionStart = $this->index + 1;
            }
        }
    }

    /**
     * Parses legacy entities (without trailing semicolon)
     *
     * @return void
     */
    protected function parseLegacyEntity() {
        $start = $this->sectionStart + 1;
        $limit = $this->index - $start;
        if ($limit > 6) {
            $limit = 6;
        } // The max length of legacy entities is 6
        while ($limit >= 2) {
            // The min length of legacy entities is 2
            $entity = substr($this->buffer, $start, $limit - $start);

            if (isset(Entity::$legacyMap[$entity])) {
                // @ts-ignore
                $this->emitPartial(Entity::$legacyMap[$entity]);
                $this->sectionStart += $limit + 1;
                return;
            } else {
                $limit--;
            }
        }
    }

    protected function stateInNamedEntity($c) {
        if ($c === ';') {
            $this->parseNamedEntityStrict();
            if ($this->sectionStart + 1 < $this->index && !$this->xmlMode) {
                $this->parseLegacyEntity();
            }
            $this->state = $this->baseState;
        } elseif (($c < 'a' || $c > 'z')
            && ($c < 'A' || $c > 'Z')
            && ($c < '0' || $c > '9')
        ) {
            if ($this->xmlMode || $this->sectionStart + 1 === $this->index) {
                // ignore
            } elseif ($this->baseState !== State::Text) {
                if ($c !== '=') {
                    $this->parseNamedEntityStrict();
                }
            } else {
                $this->parseLegacyEntity();
            }
            $this->state = $this->baseState;
            $this->index--;
        }
    }

    protected function decodeNumericEntity($offset, $base) {
        $sectionStart = $this->sectionStart + $offset;
        if ($sectionStart !== $this->index) {
            //parse entity
            $entity = substr($this->buffer, $sectionStart, $this->index - $sectionStart);
            $parsed = base_convert($entity, $base, 10);
            $this->emitPartial(Entity::decodeCodePoint($parsed));
            $this->sectionStart = $this->index;
        } else {
            $this->sectionStart--;
        }
        $this->state = $this->baseState;
    }

    protected function stateInNumericEntity($c) {
        if ($c === ';') {
            $this->decodeNumericEntity(2, 10);
            $this->sectionStart++;
        } elseif ($c < '0' || $c > '9') {
            if (!$this->xmlMode) {
                $this->decodeNumericEntity(2, 10);
            } else {
                $this->state = $this->baseState;
            }
            $this->index--;
        }
    }

    protected function stateInHexEntity($c) {
        if ($c === ';') {
            $this->decodeNumericEntity(3, 16);
            $this->sectionStart++;
        } elseif (($c < 'a' || $c > 'f')
            && ($c < 'A' || $c > 'F')
            && ($c < '0' || $c > '9')
        ) {
            if (!$this->xmlMode) {
                $this->decodeNumericEntity(3, 16);
            } else {
                $this->state = $this->baseState;
            }
            $this->index--;
        }
    }

    protected function cleanup() {
        if ($this->sectionStart < 0) {
            $this->buffer = '';
            $this->bufferOffset += $this->index;
            $this->index = 0;
        } elseif ($this->running) {
            if ($this->state === State::Text) {
                if ($this->sectionStart !== $this->index) {
                    $this->callbacks->ontext(substr($this->buffer, $this->sectionStart));
                }
                $this->buffer = '';
                $this->bufferOffset += $this->index;
                $this->index = 0;
            } elseif ($this->sectionStart === $this->index) {
                //the section just started
                $this->buffer = '';
                $this->bufferOffset += $this->index;
                $this->index = 0;
            } else {
                //remove everything unnecessary
                $this->buffer = substr($this->buffer, $this->sectionStart);
                $this->index -= $this->sectionStart;
                $this->bufferOffset += $this->sectionStart;
            }
            $this->sectionStart = 0;
        }
    }

    public function write($chunk) {
        if ($this->ended) {
            $this->callbacks->onerror(new Exception('.write() after done!'), $this->state);
        }
        $this->buffer .= $chunk;
        $this->parse();
    }

    /**
     * Iterates through the buffer, calling the function corresponding to the current state.
     * States that are more likely to be hit are higher up, as a performance improvement.
     *
     * @return void
     */
    public function parse() {
        while ($this->index < strlen($this->buffer) && $this->running) {
            $c = $this->buffer[$this->index];
            if ($this->state === State::Text) {
                $this->stateText($c);
            } elseif ($this->state === State::InAttributeValueDq) {
                $this->stateInAttributeValueDoubleQuotes($c);
            } elseif ($this->state === State::InAttributeName) {
                $this->stateInAttributeName($c);
            } elseif ($this->state === State::InComment) {
                $this->stateInComment($c);
            } elseif ($this->state === State::BeforeAttributeName) {
                $this->stateBeforeAttributeName($c);
            } elseif ($this->state === State::InTagName) {
                $this->stateInTagName($c);
            } elseif ($this->state === State::InClosingTagName) {
                $this->stateInCloseingTagName($c);
            } elseif ($this->state === State::BeforeTagName) {
                $this->stateBeforeTagName($c);
            } elseif ($this->state === State::AfterAttributeName) {
                $this->stateAfterAttributeName($c);
            } elseif ($this->state === State::InAttributeValueSq) {
                $this->stateInAttributeValueSingleQuotes($c);
            } elseif ($this->state === State::BeforeAttributeValue) {
                $this->stateBeforeAttributeValue($c);
            } elseif ($this->state === State::BeforeClosingTagName) {
                $this->stateBeforeCloseingTagName($c);
            } elseif ($this->state === State::AfterClosingTagName) {
                $this->stateAfterCloseingTagName($c);
            } elseif ($this->state === State::BeforeSpecial) {
                $this->stateBeforeSpecial($c);
            } elseif ($this->state === State::AfterComment1) {
                $this->stateAfterComment1($c);
            } elseif ($this->state === State::InAttributeValueNq) {
                $this->stateInAttributeValueNoQuotes($c);
            } elseif ($this->state === State::InSelfClosingTag) {
                $this->stateInSelfClosingTag($c);
            } elseif ($this->state === State::InDeclaration) {
                $this->stateInDeclaration($c);
            } elseif ($this->state === State::BeforeDeclaration) {
                $this->stateBeforeDeclaration($c);
            } elseif ($this->state === State::AfterComment2) {
                $this->stateAfterComment2($c);
            } elseif ($this->state === State::BeforeComment) {
                $this->stateBeforeComment($c);
            } elseif ($this->state === State::BeforeSpecialEnd) {
                $this->stateBeforeSpecialEnd($c);
            } elseif ($this->state === State::AfterScript1) {
                $this->stateAfterScript1($c);
            } elseif ($this->state === State::AfterScript2) {
                $this->stateAfterScript2($c);
            } elseif ($this->state === State::AfterScript3) {
                $this->stateAfterScript3($c);
            } elseif ($this->state === State::BeforeScript1) {
                $this->stateBeforeScript1($c);
            } elseif ($this->state === State::BeforeScript2) {
                $this->stateBeforeScript2($c);
            } elseif ($this->state === State::BeforeScript3) {
                $this->stateBeforeScript3($c);
            } elseif ($this->state === State::BeforeScript4) {
                $this->stateBeforeScript4($c);
            } elseif ($this->state === State::BeforeScript5) {
                $this->stateBeforeScript5($c);
            } elseif ($this->state === State::AfterScript4) {
                $this->stateAfterScript4($c);
            } elseif ($this->state === State::AfterScript5) {
                $this->stateAfterScript5($c);
            } elseif ($this->state === State::BeforeStyle1) {
                $this->stateBeforeStyle1($c);
            } elseif ($this->state === State::InCdata) {
                $this->stateInCdata($c);
            } elseif ($this->state === State::BeforeStyle2) {
                $this->stateBeforeStyle2($c);
            } elseif ($this->state === State::BeforeStyle3) {
                $this->stateBeforeStyle3($c);
            } elseif ($this->state === State::BeforeStyle4) {
                $this->stateBeforeStyle4($c);
            } elseif ($this->state === State::AfterStyle1) {
                $this->stateAfterStyle1($c);
            } elseif ($this->state === State::AfterStyle2) {
                $this->stateAfterStyle2($c);
            } elseif ($this->state === State::AfterStyle3) {
                $this->stateAfterStyle3($c);
            } elseif ($this->state === State::AfterStyle4) {
                $this->stateAfterStyle4($c);
            } elseif ($this->state === State::InProcessingInstruction) {
                $this->stateInProcessingInstruction($c);
            } elseif ($this->state === State::InNamedEntity) {
                $this->stateInNamedEntity($c);
            } elseif ($this->state === State::BeforeCdata1) {
                $this->stateBeforeCdata1($c);
            } elseif ($this->state === State::BeforeEntity) {
                $this->stateBeforeEntity($c);
            } elseif ($this->state === State::BeforeCdata2) {
                $this->stateBeforeCdata2($c);
            } elseif ($this->state === State::BeforeCdata3) {
                $this->stateBeforeCdata3($c);
            } elseif ($this->state === State::AfterCdata1) {
                $this->stateAfterCdata1($c);
            } elseif ($this->state === State::AfterCdata2) {
                $this->stateAfterCdata2($c);
            } elseif ($this->state === State::BeforeCdata4) {
                $this->stateBeforeCdata4($c);
            } elseif ($this->state === State::BeforeCdata5) {
                $this->stateBeforeCdata5($c);
            } elseif ($this->state === State::BeforeCdata6) {
                $this->stateBeforeCdata6($c);
            } elseif ($this->state === State::InHexEntity) {
                $this->stateInHexEntity($c);
            } elseif ($this->state === State::InNumericEntity) {
                $this->stateInNumericEntity($c);
            } elseif ($this->state === State::BeforeNumericEntity) {
                $this->stateBeforeNumericEntity($c);
            } else {
                $this->callbacks->onerror(Error('unknown protected function state'), $this->state);
            }
            $this->index++;
        }
        $this->cleanup();
    }

    public function pause() {
        $this->running = false;
    }

    public function resume() {
        $this->running = true;
        if ($this->index < strlen($this->buffer)) {
            $this->parse();
        }
        if ($this->ended) {
            $this->finish();
        }
    }

    public function end($chunk) {
        if ($this->ended) {
            $this->callbacks->onerror(new Exception('.end() after done!'), $this->state);
        }
        if ($chunk) {
            $this->write($chunk);
        }
        $this->ended = true;
        if ($this->running) {
            $this->finish();
        }
    }

    public function finish() {
        //if there is remaining data, emit it in a reasonable way
        if ($this->sectionStart < $this->index) {
            $this->handleTrailingData();
        }
        $this->callbacks->onend();
    }

    protected function handleTrailingData() {
        $data = substr($this->buffer, $this->sectionStart);
        if ($this->state === State::InCdata
            || $this->state === State::AfterCdata1
            || $this->state === State::AfterCdata2
        ) {
            $this->callbacks->oncdata($data);
        } elseif ($this->state === State::InComment
            || $this->state === State::AfterComment1
            || $this->state === State::AfterComment2
        ) {
            $this->callbacks->oncomment($data);
        } elseif ($this->state === State::InNamedEntity && !$this->xmlMode) {
            $this->parseLegacyEntity();
            if ($this->sectionStart < $this->index) {
                $this->state = $this->baseState;
                $this->handleTrailingData();
            }
        } elseif ($this->state === State::InNumericEntity && !$this->xmlMode) {
            $this->decodeNumericEntity(2, 10);
            if ($this->sectionStart < $this->index) {
                $this->state = $this->baseState;
                $this->handleTrailingData();
            }
        } elseif ($this->state === State::InHexEntity && !$this->xmlMode) {
            $this->decodeNumericEntity(3, 16);
            if ($this->sectionStart < $this->index) {
                $this->state = $this->baseState;
                $this->handleTrailingData();
            }
        } elseif ($this->state !== State::InTagName
            && $this->state !== State::BeforeAttributeName
            && $this->state !== State::BeforeAttributeValue
            && $this->state !== State::AfterAttributeName
            && $this->state !== State::InAttributeName
            && $this->state !== State::InAttributeValueSq
            && $this->state !== State::InAttributeValueDq
            && $this->state !== State::InAttributeValueNq
            && $this->state !== State::InClosingTagName
        ) {
            $this->callbacks->ontext($data);
        }
        //else, ignore remaining data
        //TODO add a way to remove current tag
    }

    public function getAbsoluteIndex() {
        return $this->bufferOffset + $this->index;
    }

    protected function getSection() {
        return substr($this->buffer, $this->sectionStart, $this->index - $this->sectionStart);
    }

    protected function emitToken($name) {
        if ($name == 'onopentagname' | $name == 'onclosetag' | $name == 'onattribdata') {
            $this->callbacks->$name($this->getSection());
            $this->sectionStart = -1;
        } else {
            throw new Exception('Emit token must be onopentagname|onclosetag|onattribdata');
        }
    }

    public function emitPartial($value) {
        if ($this->baseState !== State::Text) {
            $this->callbacks->onattribdata($value); //TODO implement the new event
        } else {
            $this->callbacks->ontext($value);
        }
    }

    public function setState($state) {
        $this->state = $state;
        return $this;
    }

    public function decIndex() {
        $this->index--;
        return $this;
    }

    public function getSectionStart() {
        return $this->sectionStart;
    }

    protected function consumeSpecialNameChar($upper, $nextState, $c) {
        $lower = strtolower($upper);

        if ($c === $lower || $c === $upper) {
            $this->state = $nextState;
        } else {
            $this->state = State::InTagName;
            $this->index--;
        }
    }

    protected function ifElseState($upper, $successState, $failureState, $c) {
        $lower = strtolower($upper);

        if ($upper === $lower) {
            if ($c === $lower) {
                $this->state = $successState;
            } else {
                $this->state = $failureState;
                $this->index--;
            }
        } else {
            if ($c === $lower || $c === $upper) {
                $this->state = $successState;
            } else {
                $this->state = $failureState;
                $this->index--;
            }
        }
    }

    public function stateBeforeStyle1($c) {
        $this->consumeSpecialNameChar('Y', State::BeforeStyle2, $c);
    }

    public function stateBeforeStyle2($c) {
        $this->consumeSpecialNameChar('L', State::BeforeStyle3, $c);
    }

    public function stateBeforeStyle3($c) {
        $this->consumeSpecialNameChar('E', State::BeforeStyle4, $c);
    }

    public function stateBeforeScript1($c) {
        $this->consumeSpecialNameChar('R', State::BeforeScript2, $c);
    }

    public function stateBeforeScript2($c) {
        $this->consumeSpecialNameChar('I', State::BeforeScript3, $c);
    }

    public function stateBeforeScript3($c) {
        $this->consumeSpecialNameChar('P', State::BeforeScript4, $c);
    }

    public function stateBeforeScript4($c) {
        $this->consumeSpecialNameChar('T', State::BeforeScript5, $c);
    }

    public function stateBeforeCdata1($c) {
        $this->ifElseState('C', State::BeforeCdata2, State::InDeclaration, $c);
    }

    public function stateBeforeCdata2($c) {
        $this->ifElseState('D', State::BeforeCdata3, State::InDeclaration, $c);
    }

    public function stateBeforeCdata3($c) {
        $this->ifElseState('A', State::BeforeCdata4, State::InDeclaration, $c);
    }

    public function stateBeforeCdata4($c) {
        $this->ifElseState('T', State::BeforeCdata5, State::InDeclaration, $c);
    }

    public function stateBeforeCdata5($c) {
        $this->ifElseState('A', State::BeforeCdata6, State::InDeclaration, $c);
    }

    public function stateAfterScript1($c) {
        $this->ifElseState('R', State::AfterScript2, State::Text, $c);
    }

    public function stateAfterScript2($c) {
        $this->ifElseState('I', State::AfterScript3, State::Text, $c);
    }

    public function stateAfterScript3($c) {
        $this->ifElseState('P', State::AfterScript4, State::Text, $c);
    }

    public function stateAfterScript4($c) {
        $this->ifElseState('T', State::AfterScript5, State::Text, $c);
    }

    public function stateAfterStyle1($c) {
        $this->ifElseState('Y', State::AfterStyle2, State::Text, $c);
    }

    public function stateAfterStyle2($c) {
        $this->ifElseState('L', State::AfterStyle3, State::Text, $c);
    }

    public function stateAfterStyle3($c) {
        $this->ifElseState('E', State::AfterStyle4, State::Text, $c);
    }

    public function stateBeforeEntity($c) {
        $this->ifElseState('#', State::BeforeNumericEntity, State::InNamedEntity, $c);
    }

    public function stateBeforeNumericEntity($c) {
        $this->ifElseState('X', State::InHexEntity, State::InNumericEntity, $c);
    }
}
