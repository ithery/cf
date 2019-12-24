<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CParser_HtmlParser implements CParser_HtmlParser_TokenizerCallbackInterface {

    use CParser_HtmlParser_ConstantTrait;
    use CParser_HtmlParser_TokenizerCallbackTrait;

    /**
     *
     * @var CParser_HtmlParser_Tokenizer 
     */
    protected $tokenizer = null;

    /**
     *
     * @var CEvent_Dispatcher
     */
    protected $dispatcher = null;
    protected $options = [];
    protected $tagName = '';
    protected $attributeName = '';
    protected $attributeValue = '';
    protected $attributes = null;
    protected $stack = [];
    protected $foreignContext = [];
    protected $lowerCaseTagNames = false;
    protected $lowerCaseAttributeNames = false;
    protected $endIndex = null;
    protected $startIndex = 0;

    public function __construct($options) {
        $this->options = $options;
        $this->tokenizer = new CParser_HtmlParser_Tokenizer($this->options, $this);
        $this->dispatcher = CEvent::createDispatcher();
        $this->rebuildConstant();

        $this->tagname = "";
        $this->attributeName = "";
        $this->attributeValue = "";
        $this->attributes = null;
        $this->stack = [];
        $this->foreignContext = [];
        $this->startIndex = 0;
        $this->endIndex = null;

        $this->lowerCaseTagNames = $this->hasOption("lowerCaseTags") ? !!$this->getOption('lowerCaseTags') : !$this->getOption('xmlMode');
        $this->lowerCaseAttributeNames = $this->hasOption("lowerCaseAttributeNames") ? !!$this->getOption('lowerCaseAttributeNames') : !$this->getOption('xmlMode');

        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnParserInit($this));
    }

    //Resets the parser to a blank state, ready to parse a new HTML document
    public function reset() {

        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnReset());

        $this->tokenizer->reset();
        $this->tagname = "";
        $this->attributeName = "";
        $this->attributes = null;
        $this->stack = [];
        $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnParserInit($this));
    }

    public function listen($events, $listener) {
        $this->dispatcher->listen($events, $listener);
    }

    //Parses a complete HTML document and pushes it to the handler
    public function parseComplete($data) {
        $this->reset();
        $this->end($data);
    }

    public function write($chunk) {
        return $this->tokenizer->write($chunk);
    }

    public function end($chunk = null) {
        return $this->tokenizer->end($chunk);
    }

    public function pause() {
        return $this->tokenizer->pause();
    }

    public function resume() {
        return $this->tokenizer->resume();
    }

    public function getOption($key, $fallback = null) {
        return carr::get($this->options, $key, $fallback);
    }

    public function hasOption($key) {
        return isset($this->options[$key]);
    }

    protected function getInstructionName($value) {
        $reNameEnd = '#\s|\/#';
        $name = $value;
        if (preg_match($reNameEnd, $value, $matches, PREG_OFFSET_CAPTURE)) {
            $idx = carr::get(carr::get($matches, 0), 1);
            $name = substr($value, 0, $idx);
        }
        if ($this->lowerCaseTagNames) {
            $name = strtolower($name);
        }


        return $name;
    }

    protected function updatePosition($initialOffset) {
        if ($this->endIndex === null) {
            if ($this->tokenizer->getSectionStart() <= $initialOffset) {
                $this->startIndex = 0;
            } else {
                $this->startIndex = $this->tokenizer->getSectionStart() - $initialOffset;
            }
        } else {
            $this->startIndex = $this->endIndex + 1;
        }
        $this->endIndex = $this->tokenizer->getAbsoluteIndex();
    }

    protected function closeCurrentTag() {
        $name = $this->tagname;
        $this->onopentagend();
        //self-closing tags will be on the top of the stack
        //(cheaper check than in onclosetag)
        $lastStackValue = carr::get($this->stack, count($this->stack) - 1);

        if ($lastStackValue === $name) {
            if ($this->dispatcher->hasListeners(CParser_HtmlParser_Event_OnCloseTag::class)) {

                $this->dispatcher->dispatch(new CParser_HtmlParser_Event_OnCloseTag($name));
            }
            array_pop($this->stack);
        }
    }

}
