<?php

defined('SYSPATH') or die('No direct access allowed.');

use Illuminate\Contracts\Support\Arrayable;

/**
 * Mutable collection of CElement_Component_Calendar_CalendarEvent, used both ways a
 * CElement_Component_Calendar populates events:
 * - directly by the developer, via CElement_Component_Calendar::createEvents() before render,
 *   for a fixed (non-ajax) event list.
 * - by the CElement_Component_Calendar::setCallback() callback, which CAjax_Engine_Calendar
 *   invokes with a fresh instance per ajax request and reads back via getData().
 */
class CElement_Component_Calendar_CalendarEvents implements Arrayable {
    /**
     * @var CCollection<CElement_Component_Calendar_CalendarEvent>
     */
    protected $data;

    /**
     * @return void
     */
    public function __construct() {
        $this->data = c::collect();
    }

    /**
     * Replace the whole event list.
     *
     * @param array $data list of fullCalendar-shaped event arrays
     *
     * @return $this
     */
    public function setData(array $data) {
        $this->data = c::collect();

        foreach ($data as $event) {
            $this->addData($event);
        }

        return $this;
    }

    /**
     * Append one event to the list.
     *
     * @param array $data a single fullCalendar-shaped event array
     *
     * @return $this
     */
    public function addData(array $data) {
        $this->data->push(new CElement_Component_Calendar_CalendarEvent($data));

        return $this;
    }

    /**
     * @return CCollection<CElement_Component_Calendar_CalendarEvent>
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return array
     */
    public function toArray() {
        return $this->data->toArray();
    }
}
