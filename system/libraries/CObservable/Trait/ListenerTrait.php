<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CObservable_Trait_ListenerTrait {
    /**
     * @var CObservable_Listener[]
     */
    protected $listeners;

    public function getListeners() {
        return $this->listeners;
    }

    /**
     * @param string $event
     *
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

    /**
     * @param string $event
     *
     * @return bool
     */
    public function haveListener($event) {
        foreach ($this->listeners as $listener) {
            if ($listener->getEvent() == $event) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $event
     *
     * @return CObservable_Listener[]
     */
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
     * @return CObservable_Listener_ReadyListener
     */
    public function onReadyListener() {
        if (!isset($this->listeners['ready'])) {
            $this->listeners['ready'] = new CObservable_Listener_ReadyListener($this->id);
        }

        return $this->listeners['ready'];
    }

    /**
     * @return CObservable_Listener_ClickListener
     */
    public function onClickListener() {
        if (!isset($this->listeners['click'])) {
            $this->listeners['click'] = new CObservable_Listener_ClickListener($this->id);
        }

        return $this->listeners['click'];
    }

    /**
     * @return CObservable_Listener_ChangeListener
     */
    public function onChangeListener() {
        if (!isset($this->listeners['change'])) {
            $this->listeners['change'] = new CObservable_Listener_ChangeListener($this->id);
        }

        return $this->listeners['change'];
    }

    /**
     * @return CObservable_Listener_MouseUpListener
     */
    public function onMouseUpListener() {
        if (!isset($this->listeners['mouseUp'])) {
            $this->listeners['mouseUp'] = new CObservable_Listener_MouseUpListener($this->id);
        }

        return $this->listeners['mouseUp'];
    }

    /**
     * @return CObservable_Listener_MouseUpListener
     */
    public function onMouseDownListener() {
        if (!isset($this->listeners['mouseDown'])) {
            $this->listeners['mouseDown'] = new CObservable_Listener_ChangeListener($this->id);
        }

        return $this->listeners['mouseDown'];
    }
}
