<?php

class CCompponent_Feature_SupportRootElementTracking {
    public static function init() {
        return new static();
    }

    public function __construct() {
        CComponent_Manager::instance()->listen('component.dehydrate.initial', function ($component, $response) {
            if (!$html = c::get($response, 'effects.html')) {
                return;
            }

            c::set($response, 'effects.html', $this->addComponentEndingMarker($html, $component));
        });
    }

    public function addComponentEndingMarker($html, $component) {
        return $html . "\n<!-- Cresenity Component cres-end:" . $component->id . ' -->';
    }

    public static function stripOutEndingMarker($html) {
        return preg_replace('/<!-- Cresenity Component cres-end:.*? -->/', '', $html);
    }
}
