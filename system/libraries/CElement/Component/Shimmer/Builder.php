<?php

class CElement_Component_Shimmer_Builder {
    /**
     * @var CCollection|CElement_Component_Shimmer_Element[];
     */
    protected $elements;

    public function __construct() {
        $this->elements = c::collect();
    }

    public function col($className = '', $builderCallback = null) {
        $className = str_replace('col-', 'cres-ph-col-', $className);

        return $this->addElement('cres-ph-col ' . $className, $builderCallback);
    }

    public function row($className = '', $builderCallback = null) {
        $className = str_replace('row-', 'cres-ph-row-', $className);

        return $this->addElement('cres-ph-row ' . $className, $builderCallback);
    }

    public function img($className = '', $builderCallback = null) {
        return $this->addElement('cres-ph-picture ' . $className, $builderCallback);
    }

    public function addElement($className, $builderCallback) {
        $this->elements->push(new CElement_Component_Shimmer_Element($className, $builderCallback));

        return $this;
    }

    public function toHtml() {
        return $this->elements->reduce(function ($carry, CElement_Component_Shimmer_Element $item, $key) {
            return $carry . $item->toHtml();
        }, '');
    }
}
