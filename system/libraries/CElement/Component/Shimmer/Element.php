<?php

class CElement_Component_Shimmer_Element {
    /**
     * @var null|CElement_Component_Shimmer_Builder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param string        $class
     * @param callable|null $builderCallback
     *
     * @return void
     */
    public function __construct($class, $builderCallback) {
        $this->class = $class;
        if ($builderCallback) {
            $builderCallback($this->builder = new CElement_Component_Shimmer_Builder());
        }
    }

    /**
     * @return string
     */
    public function toHtml() {
        $html = '<div class="' . $this->class . '">';
        if ($this->builder) {
            $html .= $this->builder->toHtml();
        }

        $html .= '</div>';

        return $html;
    }
}
