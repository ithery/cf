<?php
use Opis\Closure\SerializableClosure;

class CElement_Depends_DependsOn {
    use CTrait_HasOptions;

    protected $selector;

    protected $resolver;

    public function __construct($selector, $resolver, $options = []) {
        $this->options = $options;

        $this->setResolver($resolver);
        $this->setSelector($selector);
    }

    public function setSelector($selector) {
        if ($selector instanceof CRenderable) {
            $selector = '#' . $selector->id();
        }
        $this->selector = $selector;

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
}
