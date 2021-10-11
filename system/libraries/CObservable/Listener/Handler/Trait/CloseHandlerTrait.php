<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 6:36:18 PM
 */
trait CObservable_Listener_Handler_Trait_CloseHandlerTrait {
    public function onCloseListener() {
        if (!isset($this->handlerListeners['close'])) {
            $this->handlerListeners['close'] = new CObservable_Listener_Pseudo_CloseListener($this);
        }
        return $this->handlerListeners['close'];
    }

    public function haveCloseListener() {
        return $this->haveListener('close');
    }

    public function getCloseListener() {
        return $this->getListener('close');
    }
}
