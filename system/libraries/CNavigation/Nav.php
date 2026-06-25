<?php

use Illuminate\Contracts\Support\Arrayable;

class CNavigation_Nav implements Arrayable {
    /**
     * @var string
     */
    protected $name;

    /**
     * @var null|array
     */
    protected $data;

    /**
     * @param string     $name
     * @param null|array $data
     */
    public function __construct($name, $data) {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return null|array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return null|array
     */
    public function toArray() {
        return $this->getData();
    }

    /**
     * @param null|CNavigation_RendererInterface $renderer
     *
     * @return void
     */
    public function render($renderer = null) {
    }
}
