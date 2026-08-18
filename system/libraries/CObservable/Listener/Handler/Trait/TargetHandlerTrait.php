<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CObservable_Listener_Handler_Trait_TargetHandlerTrait {
    /**
     * Id of handler targeted renderable.
     *
     * @var string
     */
    protected $target;

    /**
     * Set the target of the handler.
     *
     * @param string|CObservable|CRenderable $target
     *
     * @return $this
     */
    public function setTarget($target) {
        if ($target instanceof CObservable) {
            if (get_class($this) == CObservable_Listener_Handler_ReloadHandler::class) {
                if ($target->haveReloadHandler()) {
                    $reloadHandler = $target->reloadHandler();
                    if (c::hasTrait($this, CObservable_Listener_Handler_Trait_AjaxHandlerTrait::class)
                        && c::hasTrait($reloadHandler, CObservable_Listener_Handler_Trait_AjaxHandlerTrait::class)
                    ) {
                        if (strlen($this->url) == 0) {
                            $this->url = $reloadHandler->getUrl();
                        }
                    }
                }
            }
        }
        if ($target instanceof CRenderable) {
            $target = $target->id();
        }
        $this->target = $target;

        return $this;
    }
}
