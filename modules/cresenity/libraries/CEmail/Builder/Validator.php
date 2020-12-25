<?php

class CEmail_Builder_Validator {
    protected $options;
    protected $element;

    public function __construct(CEmail_Builder_Node $element, $options = []) {
        $this->element = $element;
        $this->options = $options;
    }

    public function getOption($key, $defaultValue = null) {
        return carr::get($this->options, $key, $defaultValue);
    }

    public function validate() {
        $errors = [];
        $defaultSkipElements = 'cml';
        $skipElements = $this->getOption('skipElements', $defaultSkipElements);
        if (!in_array($this->element->tagName, $skipElements)) {
            //carr::flatten(carr::concat($erros,));
        }
    }
}
