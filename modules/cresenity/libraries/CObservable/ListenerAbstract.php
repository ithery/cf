<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 17, 2019, 11:31:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CObservable_ListenerAbstract {

    protected $owner;
    protected $handlers;

    public function __construct($owner) {
        $this->owner = $owner;
        $this->handlers = array();
    }

    public function owner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
        //we set all handler owner too
        foreach ($this->handlers as $handler) {
            $handler->setOwner($owner);
        }
        return $this;
    }

    public function handlers() {
        return $this->handlers;
    }

    /**
     * 
     * @param type $param
     */
    public function setHandlerUrlParam($param) {

        foreach ($this->handlers as $handler) {
            $handler->setUrlParam($param);
        }
    }

    /**
     * 
     * @param string $handlerName
     * @return CObservable_Listener_Handler
     */
    public function addHandler($handlerName) {
        $handler = new CObservable_Listener_Handler($this->owner, $this->event, $handlerName);
        $this->handlers[] = $handler;
        return $handler;
    }

}
