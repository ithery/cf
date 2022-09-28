<?php

class CElement_Component_Shimmer_Element {
    /**
     * @var null|CElement_Component_Shimmer_Builder
     */
    protected $builder;

    public function __construct($class, $builderCallback) {
        $this->class = $class;
        if ($builderCallback) {
            $builderCallback($this->builder = new CElement_Component_Shimmer_Builder());
        }
    }

    public function toHtml() {
        $html = '<div class="' . $this->class . '">';
        if ($this->builder) {
            $html .= $this->builder->toHtml();
        }

        $html .= '</div>';

        return $html;
    }
}
