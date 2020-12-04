<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_Event {

    protected $name;
    protected $params;
    protected $up;
    protected $self;
    protected $component;

    public function __construct($name, $params) {
        $this->name = $name;
        $this->params = $params;
    }

    public function up() {
        $this->up = true;

        return $this;
    }

    public function self() {
        $this->self = true;

        return $this;
    }

    public function component($name) {
        $this->component = $name;

        return $this;
    }

    public function to() {
        return $this;
    }

    public function serialize() {
        $output = [
            'event' => $this->name,
            'params' => $this->params,
        ];

        if ($this->up)
            $output['ancestorsOnly'] = true;
        if ($this->self)
            $output['selfOnly'] = true;
        if ($this->component)
            $output['to'] = $this->component;

        return $output;
    }

}
