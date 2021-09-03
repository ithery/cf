<?php

class CComponent_RenameMe_SupportEvents
{
    static function init() { return new static; }

    function __construct()
    {
        CComponent_Manager::instance()->listen('component.hydrate', function ($component, $request) {
            //
        });

        CComponent_Manager::instance()->listen('component.dehydrate.initial', function ($component, $response) {
            $response->effects['listeners'] = $component->getEventsBeingListenedFor();
        });

        CComponent_Manager::instance()->listen('component.dehydrate', function ($component, $response) {
            $emits = $component->getEventQueue();
            $dispatches = $component->getDispatchQueue();

            if ($emits) {
                $response->effects['emits'] = $emits;
            }

            if ($dispatches) {
                $response->effects['dispatches'] = $dispatches;
            }
        });
    }
}
