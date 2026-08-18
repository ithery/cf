<?php

class CPrinter_EscPos_Parser {
    const EVENT_CHAR = 'char';

    const EVENT_ESC = 'esc';

    const EVENT_END = 'end';

    const EVENT_FEED_FORM = 'feedForm';

    const EVENT_GROUP_SEPARATOR = 'groupSeparator';

    protected $input;

    protected $position;

    protected $listeners = [];

    private $currentChar;

    public function __construct($input) {
        $this->input = $input;
        $this->position = 0;
        $this->currentChar = $this->input[$this->position] ?? null;
        $this->listeners = [];
    }

    public function parse() {
        while ($this->currentChar !== null) {
            if ($this->currentChar == CPrinter_EscPos::ESC) {
                $this->onEsc();
            } elseif ($this->currentChar == CPrinter_EscPos::GS) {
                $this->onGroupSeparator();
            } elseif ($this->currentChar == CPrinter_EscPos::FF) {
                $this->onFeedForm();
            } else {
                $this->onChar();
            }
            $this->advance();
        }
        $this->onEnd();
    }

    public function advance() {
        $this->position++;
        $this->currentChar = $this->input[$this->position] ?? null;
    }

    public function advanceUntil($char) {
        $advanced = '';
        while ($this->currentChar !== $char && $this->currentChar != null) {
            $this->advance();
            $advanced .= $this->currentChar;
        }

        return $advanced;
    }

    public function advanceFor($len) {
        $advanced = '';
        for ($i = 0; $i < $len; $i++) {
            $this->advance();
            if ($this->currentChar == null) {
                break;
            }
            $advanced .= $this->currentChar;
        }

        return $advanced;
    }

    private function onEsc() {
        $this->dispatch(self::EVENT_ESC);
    }

    private function onFeedForm() {
        $this->dispatch(self::EVENT_FEED_FORM);
    }

    private function onGroupSeparator() {
        $this->dispatch(self::EVENT_GROUP_SEPARATOR);
    }

    private function onChar() {
        $this->dispatch(self::EVENT_CHAR);
    }

    private function onEnd() {
        $this->dispatch(self::EVENT_END);
    }

    public function dispatch($event) {
        if (isset($this->listeners[$event])) {
            return Closure::fromCallable($this->listeners[$event])->__invoke($this);
        }

        return null;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getCurrentChar() {
        return $this->currentChar;
    }

    public function getNextChar() {
        return $this->input[$this->position + 1] ?? null;
    }

    public function on($event, Closure $callback) {
        $this->listeners[$event] = $callback;

        return $this;
    }
}
