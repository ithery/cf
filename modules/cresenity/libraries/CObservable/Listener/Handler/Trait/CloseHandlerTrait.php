<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 6:36:18 PM
 * @license Ittron Global Teknologi <ittron.co.id>
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
