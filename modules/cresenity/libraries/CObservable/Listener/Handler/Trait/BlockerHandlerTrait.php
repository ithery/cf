<?php

/**
 * Description of BlockerHandlerTrait
 *
 * @author Hery
 */

trait CObservable_Listener_Handler_Trait_BlockerHandlerTrait {

    /**
     * id of handler targeted renderable
     * @var string
     */
    protected $blocker;

    public function setBlocker($blocker) {
        $this->blocker = $blocker;

        return $this;
    }

    
    public function getBlockHtml() {
        $html = $this->blocker;
        return $html;
    }
}
