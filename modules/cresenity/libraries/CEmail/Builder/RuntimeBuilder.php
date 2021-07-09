<?php

/**
 * @mixed CEmail_Builder_Node
 */
class CEmail_Builder_RuntimeBuilder {
    /**
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

    public function render($options = []) {
        $options;
        $parser = new CEmail_Builder_Parser($this->node, $options);
        return $parser->parse();
    }
}
