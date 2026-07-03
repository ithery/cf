<?php

class CElement_Component_Shimmer extends CElement_Component {
    /**
     * @var CElement_Component_Shimmer_Builder|null
     */
    protected $builder;

    /**
     * @param string|null $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);
    }

    /**
     * @param string|null $id
     *
     * @return static
     */
    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function withBuilder($callback) {
        $callback($this->builder());

        return $this;
    }

    /**
     * @return CElement_Component_Shimmer_Builder
     */
    public function builder() {
        if ($this->builder == null) {
            $this->builder = new CElement_Component_Shimmer_Builder();
        }

        return $this->builder;
    }

    /**
     * @return void
     */
    protected function build() {
        $this->addClass('cres:element:component:Shimmer');
        $this->setAttr('cres-element', 'component:Shimmer');
        $this->add($this->builder()->toHtml());
    }
}
