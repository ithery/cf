<?php

class CElement_Component_Shimmer extends CElement_Component {
    protected $skeleton;

    public function __construct() {
        parent::__construct();
    }

    public function withBuilder($callback) {
        $builder = new CElement_Component_Shimmer_Builder();
        $callback($builder);
        $html = $builder->toHtml();
    }
}
