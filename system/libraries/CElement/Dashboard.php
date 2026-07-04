<?php

/**
 * @deprecated dont use anymore
 */
class CElement_Dashboard extends CElement_Element {
    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $id
     * @param array  $options
     *
     * @return void
     */
    public function __construct($id, $options) {
        parent::__construct($id);
        $this->options = $options;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function opt($key) {
        return carr::get($this->options, $key);
    }
}
