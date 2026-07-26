<?php
/**
 * @see CElement_Component_Shimmer
 */
class CElement_Component_Shimmer_Builder {
    /**
     * @var CCollection<CElement_Component_Shimmer_Element>
     */
    protected $elements;

    /**
     * @return void
     */
    public function __construct() {
        $this->elements = c::collect();
    }

    /**
     * @param string        $className
     * @param callable|null $builderCallback
     *
     * @return $this
     */
    public function col($className = '', $builderCallback = null) {
        $className = str_replace('col-', 'shimmer-col-', $className);

        return $this->addElement('shimmer-col ' . $className, $builderCallback);
    }

    /**
     * @param string        $className
     * @param callable|null $builderCallback
     *
     * @return $this
     */
    public function row($className = '', $builderCallback = null) {
        return $this->addElement('shimmer-row ' . $className, $builderCallback);
    }

    /**
     * @param string $className
     *
     * @return $this
     */
    public function spacing($className = '') {
        return $this->addElement('shimmer-spacing ' . $className);
    }

    /**
     * @param string $className
     *
     * @return $this
     */
    public function img($className = '') {
        return $this->addElement('shimmer-picture ' . $className);
    }

    /**
     * @param string $className
     *
     * @return $this
     */
    public function avatar($className = '') {
        return $this->addElement('shimmer-avatar ' . $className);
    }

    /**
     * @param string        $className
     * @param callable|null $builderCallback
     *
     * @return $this
     */
    public function addElement($className, $builderCallback = null) {
        $this->elements->push(new CElement_Component_Shimmer_Element($className, $builderCallback));

        return $this;
    }

    /**
     * @return string
     */
    public function toHtml() {
        return $this->elements->reduce(function ($carry, CElement_Component_Shimmer_Element $item, $key) {
            return $carry . $item->toHtml();
        }, '');
    }
}
