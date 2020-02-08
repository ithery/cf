<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @mixed CEmail_Builder_Node
 */
class CEmail_Builder_RuntimeBuilder {

    /**
     *
     * @var CEmail_Builder_Node
     */
    protected $node;

    public function __construct() {
       
        $this->node = new CEmail_Builder_Node(['tagName' => 'cml']);
    }

    public function __call($method, $args) {
        if (method_exists($this->node, $method)) {
            return call_user_func_array([$this->node, $method], $args);
        }
        throw new Exception('not defined method ' . $method);
    }

    public function render($options=[]) {
        $options;
        $parser = new CEmail_Builder_Parser($this->node, $options);
        return $parser->parse();
    }

    
}
