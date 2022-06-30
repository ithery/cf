<?php
/**
 * @see CElement_Component_Shimmer
 */
class CElement_Component_Shimmer_Builder {
    /**
     * @var CCollection|CElement_Component_Shimmer_Element[];
     */
    protected $elements;

    public function __construct() {
        $this->elements = c::collect();
    }

    public function col($className = '', $builderCallback = null) {
        $className = str_replace('col-', 'shimmer-col-', $className);

        return $this->addElement('shimmer-col ' . $className, $builderCallback);
    }

    public function row($className = '', $builderCallback = null) {
        return $this->addElement('shimmer-row ' . $className, $builderCallback);
    }

    public function spacing($className = '') {
        return $this->addElement('shimmer-spacing ' . $className);
    }

    public function img($className = '') {
        return $this->addElement('shimmer-picture ' . $className);
    }

    public function avatar($className = '') {
        return $this->addElement('shimmer-avatar ' . $className);
    }

    public function addElement($className, $builderCallback = null) {
        $this->elements->push(new CElement_Component_Shimmer_Element($className, $builderCallback));

        return $this;
    }

    public function toHtml() {
        return $this->elements->reduce(function ($carry, CElement_Component_Shimmer_Element $item, $key) {
            return $carry . $item->toHtml();
        }, '');
    }
}
