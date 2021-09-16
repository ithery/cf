<?php

class CComponent_RenameMe_Placeholder {
    public static function init() {
        return new static;
    }

    public function __construct() {
        CComponent_Manager::instance()->listen('component.hydrate', function ($component, $request) {
            //
        });

        CComponent_Manager::instance()->listen('component.dehydrate', function ($component, $response) {
            //
        });
    }
}
