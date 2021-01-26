<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 1:19:47 PM
 */
trait CObservable_Listener_Trait_HandlerTrait {
    /**
     * @return \CObservable_Listener_Handler_ReloadHandler
     */
    public function addReloadHandler() {
        $handler = new CObservable_Listener_Handler_ReloadHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }
    
    
    /**
     * 
     * @return \CObservable_Listener_Handler_ReloadElementHandler
     */
    public function addReloadElementHandler() {
        $handler = new CObservable_Listener_Handler_ReloadElementHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_ReloadDataTableHandler
     */
    public function addReloadDataTableHandler() {
        $handler = new CObservable_Listener_Handler_ReloadDataTableHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_AppendHandler
     */
    public function addAppendHandler() {
        $handler = new CObservable_Listener_Handler_AppendHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_PrependHandler
     */
    public function addPrependHandler() {
        $handler = new CObservable_Listener_Handler_PrependHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_DialogHandler
     */
    public function addDialogHandler() {
        $handler = new CObservable_Listener_Handler_DialogHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_CloseDialogHandler
     */
    public function addCloseDialogHandler() {
        $handler = new CObservable_Listener_Handler_CloseDialogHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_AjaxSubmitHandler
     */
    public function addAjaxSubmitHandler() {
        $handler = new CObservable_Listener_Handler_AjaxSubmitHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_AjaxHandler
     */
    public function addAjaxHandler() {
        $handler = new CObservable_Listener_Handler_AjaxHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_RemoveHandler
     */
    public function addRemoveHandler() {
        $handler = new CObservable_Listener_Handler_RemoveHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_ToastHandler
     */
    public function addToastHandler() {
        $handler = new CObservable_Listener_Handler_ToastHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }

    /**
     * @return \CObservable_Listener_Handler_CustomHandler
     */
    public function addCustomHandler() {
        $handler = new CObservable_Listener_Handler_CustomHandler($this);
        $this->handlers[] = $handler;
        return $handler;
    }
}
