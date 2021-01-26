<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
trait CComponent_Concern_ReceivesEventsTrait {

    protected $eventQueue = [];
    protected $dispatchQueue = [];
    protected $listeners = [];

    protected function getListeners() {
        return $this->listeners;
    }

    public function emit($event, ...$params) {
        return $this->eventQueue[] = new CComponent_Event($event, $params);
    }

    public function emitUp($event, ...$params) {
        $this->emit($event, ...$params)->up();
    }

    public function emitSelf($event, ...$params) {
        $this->emit($event, ...$params)->self();
    }

    public function emitTo($name, $event, ...$params) {
        $this->emit($event, ...$params)->component($name);
    }

    public function dispatchBrowserEvent($event, $data = null) {
        $this->dispatchQueue[] = [
            'event' => $event,
            'data' => $data,
        ];
    }

    public function getEventQueue() {
        return c::collect($this->eventQueue)->map->serialize()->toArray();
    }

    public function getDispatchQueue() {
        return $this->dispatchQueue;
    }

    protected function getEventsAndHandlers() {
        return c::collect($this->getListeners())
                        ->mapWithKeys(function ($value, $key) {
                            $key = is_numeric($key) ? $value : $key;

                            return [$key => $value];
                        })->toArray();
    }

    public function getEventsBeingListenedFor() {
        return array_keys($this->getEventsAndHandlers());
    }

    public function fireEvent($event, $params) {
        $method = $this->getEventsAndHandlers()[$event];

        $this->callMethod($method, $params);
    }

}
