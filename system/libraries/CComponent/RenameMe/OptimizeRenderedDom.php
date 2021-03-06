<?php

class CComponent_RenameMe_OptimizeRenderedDom {
    public static function init() {
        return new static;
    }

    protected $htmlHashesByComponent = [];

    public function __construct() {
        CComponent_Manager::instance()->listen('component.dehydrate.initial', function ($component, $response) {
            $response->memo['htmlHash'] = hash('crc32b', $response->effects['html']);
        });

        CComponent_Manager::instance()->listen('component.hydrate.subsequent', function ($component, $request) {
            $this->htmlHashesByComponent[$component->id] = $request->memo['htmlHash'];
        });

        CComponent_Manager::instance()->listen('component.dehydrate.subsequent', function ($component, $response) {
            $oldHash = isset($this->htmlHashesByComponent[$component->id]) ? $this->htmlHashesByComponent[$component->id] : null;

            $response->memo['htmlHash'] = $newHash = hash('crc32b', $response->effects['html']);

            if ($oldHash === $newHash) {
                $response->effects['html'] = null;
            }
        });
    }
}
