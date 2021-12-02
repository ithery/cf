<?php

class CComponent_RenameMe_SupportChildren {
    public static function init() {
        return new static;
    }

    public function __construct() {
        CComponent_Manager::instance()->listen('component.dehydrate', function ($component, $response) {
            $response->memo['children'] = $component->getRenderedChildren();
        });

        CComponent_Manager::instance()->listen('component.hydrate.subsequent', function ($component, $request) {
            $component->setPreviouslyRenderedChildren($request->memo['children']);
        });
    }
}
