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
        if ($target instanceof CRenderable) {
            $target = $target->id();
        }
        $this->target = $target;

        return $this;
    }
}
