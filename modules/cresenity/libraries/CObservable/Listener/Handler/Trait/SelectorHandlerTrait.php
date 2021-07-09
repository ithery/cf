<?php

trait CObservable_Listener_Handler_Trait_SelectorHandlerTrait {
    /**
     * Query selector of handler targeted renderable
     *
     * @var string
     */
    protected $selector;

    public function setSelector($selector) {
        $this->selector = $selector;

        return $this;
    }

    public function getSelector() {
        if ($this->selector != null) {
            return $this->selector;
        }

        if (c::hasTrait($this, CObservable_Listener_Handler_Trait_TargetHandlerTrait::class)) {
            if (strlen($this->target) > 0) {
                return '#' . $this->target;
            }
        }
        return '#';
    }
}
