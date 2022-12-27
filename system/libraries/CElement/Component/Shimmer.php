<?php

class CElement_Component_Shimmer extends CElement_Component {
    protected $builder;

    public function __construct() {
        parent::__construct();
    }

    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    public function withBuilder($callback) {
        $callback($this->builder());

        return $this;
    }

    public function builder() {
        if ($this->builder == null) {
            $this->builder = new CElement_Component_Shimmer_Builder();
        }

        return $this->builder;
    }

    protected function build() {
        $this->addClass('cres:element:component:Shimmer');
        $this->setAttr('cres-element', 'component:Shimmer');
        $this->add($this->builder()->toHtml());
    }
}
