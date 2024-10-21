<?php
use Opis\Closure\SerializableClosure;

/**
 * @see CElement_Element_Div
 * @see CElement_FormInput_Select
 * @see CElement_FormInput_SelectSearch
 */
class CElement_Depends_DependsOn {
    use CTrait_HasOptions;

    /**
     * @var CElement_Depends_Selector
     */
    protected $selector;

    protected $resolver;

    public function __construct($selector, $resolver, $options = []) {
        $this->options = $options;

        $this->setResolver($resolver);
        $this->selector = new CElement_Depends_Selector(carr::wrap($selector));
    }

    public function addSelector($selector) {
        $this->selector->addSelector($selector);

        return $this;
    }

    public function setSelector($selector) {
        $this->selector->setSelectors($selector);

        return $this;
    }

    public function setResolver($resolver) {
        $this->resolver = new SerializableClosure($resolver);

        return $this;
    }

    public function getSelector() {
        return $this->selector;
    }

    public function getResolver() {
        return $this->resolver;
    }

    public function getThrottle() {
        return $this->getOption('throttle', 100);
    }

    public function getBlock() {
        return $this->getOption('block', true);
    }
}
