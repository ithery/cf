<?php

class CComponent_RenameMe_SupportCollections {
    public static function init() {
        return new static;
    }

    public function __construct() {
        CComponent_Manager::instance()->listen('property.dehydrate', function ($name, $value, $component, $response) {
            if (!$value instanceof CCollection || $value instanceof CModel_Collection) {
                return;
            }
        });

        CComponent_Manager::instance()->listen('property.hydrate', function ($name, $value, $component, $request) {
            $collections = c::get($request->memo, 'dataMeta.collections', []);

            foreach ($collections as $name) {
                c::set($component, $name, c::collect(c::get($component, $name)));
            }
        });
    }
}
