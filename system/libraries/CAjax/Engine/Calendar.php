<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Ajax engine backing CElement_Component_Calendar's ajax mode: requires the files
 * registered via CElement_Component_Calendar::setEvents()'s $require argument, then
 * invokes that callback with ($startDate, $endDate, CElement_Component_Calendar_CalendarEvents),
 * returning the events the callback pushed into it as JSON.
 */
class CAjax_Engine_Calendar extends CAjax_Engine {
    /**
     * @return CHTTP_JsonResponse
     */
    public function execute() {
        $data = $this->getData();
        $input = $this->getInput();

        foreach (carr::get($data, 'requires', []) as $require) {
            if (file_exists($require)) {
                require_once $require;
            }
        }

        $callback = unserialize(carr::get($data, 'callback'));

        $events = new CElement_Component_Calendar_CalendarEvents();
        c::call($callback, [carr::get($input, 'start'), carr::get($input, 'end'), $events]);

        return c::response()->json($events->getData());
    }
}
