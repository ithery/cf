<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 1:36:31 PM
 */
trait CObservable_Listener_Handler_Trait_TargetHandlerTrait {
    /**
     * Id of handler targeted renderable
     *
     * @var string
     */
    protected $target;

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
