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
        $listener = new CObservable_Listener($this->id, $event);
        $this->listeners[] = $listener;
        return $listener;
    }

    public function haveListener($event) {
        foreach ($this->listeners as $listener) {
            if ($listener->getEvent() == $event) {
                return true;
            }
        }
        return false;
    }

}
