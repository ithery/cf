<?php

class CComponent_RenameMe_SupportValidation {
    public static function init() {
        return new static;
    }

    public function __construct() {
        CComponent_Manager::instance()->listen('component.dehydrate', function ($component, $response) {
            $errors = $component->getErrorBag()->toArray();

            // Only persist errors that were born from properties on the component
            // and not from custom validators (Validator::make) that were run.
            $response->memo['errors'] = c::collect($errors)
                ->filter(function ($value, $key) use ($component) {
                    return $component->hasProperty($key);
                })
                ->toArray();
        });

        CComponent_Manager::instance()->listen('component.hydrate', function ($component, $request) {
            $component->setErrorBag(
                isset($request->memo['errors']) ? $request->memo['errors'] : []
            );
        });
    }
}
