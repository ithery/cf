<?php

class CComponent_RenameMe_SupportLocales {
    public static function init() {
        return new static();
    }

    public function __construct() {
        CComponent_Manager::instance()->listen('component.dehydrate.initial', function ($component, $response) {
            $response->fingerprint['locale'] = CF::getLocale();
        });

        CComponent_Manager::instance()->listen('component.hydrate.subsequent', function ($component, $request) {
            if ($locale = $request->fingerprint['locale']) {
                CF::setLocale($locale);
            }
        });
    }
}
