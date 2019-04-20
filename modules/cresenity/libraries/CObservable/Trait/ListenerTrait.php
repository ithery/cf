<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 11:02:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Trait_ListenerTrait {

    /**
     *
     * @var CObservable_Listener[]
     */
    protected $listeners;

    public function getListeners() {
        return $this->listeners;
    }

    /**
     * 
     * @param string $event
     * @return CObservable_Listener
     */
    public function addListener($event) {
        if (!isset($this->listeners[$event])) {
            switch ($event) {
                default:
                    $listener = new CObservable_Listener($this->id, $event);
            }
            $this->listeners[$event] = $listener;
        }
        return $this->listeners[$event];
    }

    public function haveListener($event) {
        foreach ($this->listeners as $listener) {
            if ($listener->getEvent() == $event) {
                return true;
            }
        }
        return false;
    }

    public function getListenersByEvent($event) {
        $return = [];
        foreach ($this->listeners as $listener) {
            if ($listener->getEvent() == $event) {
                $return[] = $listener;
            }
        }
        return $return;
    }

    /**
     * 
     * @return CObservable_Listener_ClickListener
     */
    public function onClickListener() {
        if (!isset($this->listeners['click'])) {
            $this->listeners['click'] = new CObservable_Listener_ClickListener($this->id);
        }
        return $this->listeners['click'];
    }
    
     /**
     * 
     * @return CObservable_Listener_ClickListener
     */
    public function onChangeListener() {
        if (!isset($this->listeners['change'])) {
            $this->listeners['change'] = new CObservable_Listener_ChangeListener($this->id);
        }
        return $this->listeners['change'];
    }

}
