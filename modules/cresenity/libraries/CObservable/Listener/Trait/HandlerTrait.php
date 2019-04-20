<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 1:19:47 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Listener_Trait_HandlerTrait {

    public function addReloadHandler() {
        $handler = new CObservable_Listener_Handler_ReloadHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    public function addAppendHandler() {
        $handler = new CObservable_Listener_Handler_AppendHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    public function addPrependHandler() {
        $handler = new CObservable_Listener_Handler_PrependHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    public function addDialogHandler() {
        $handler = new CObservable_Listener_Handler_DialogHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

}
