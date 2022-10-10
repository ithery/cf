<?php

/**
 * @deprecated dont use anymore
 */
class CElement_Dashboard extends CElement_Element {
    protected $options;

    public function __construct($id, $options) {
        parent::__construct($id);
        $this->options = $options;
    }

    public function opt($key) {
        return carr::get($this->options, $key);
    }
}
