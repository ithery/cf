<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_ViewComponent extends CElement {
    /**
     * @var string
     */
    protected $component;

    /**
     * @var array
     */
    protected $data;

    /**
     * @phpstan-ignore-next-line
     *
     * @param mixed $id
     * @param mixed $component
     * @param mixed $options
     */
    public function __construct($id, $component, $options = []) {
        parent::__construct($id);
        if ($component != null) {
            $this->setComponent($component);
        }
        $this->data = [];
    }

    public function setComponent($component, $options = []) {
        $this->component = $component;
    }

    public function html($indent = 0) {
        if ($this->component != null) {
            return CApp::component()->getHtml($this->component, $this->data);
        }
    }

    public function js($indent = 0) {
        return '';
    }

    public function setData(array $data) {
        $this->data = $data;

        return $this;
    }
}
