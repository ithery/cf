<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_HydrationMiddleware_PerformDataBindingUpdates implements CComponent_HydrationMiddlewareInterface {
    public static function hydrate($unHydratedInstance, $request) {
        try {
            foreach ($request->updates as $update) {
                if ($update['type'] !== 'syncInput') {
                    continue;
                }

                $data = $update['payload'];

                $unHydratedInstance->syncInput($data['name'], $data['value']);
            }
        } catch (CValidation_Exception $e) {
            CComponent_Manager::instance()->dispatch('failed-validation', $e->validator);

            $unHydratedInstance->setErrorBag($e->validator->errors());
        }
    }

    public static function dehydrate($instance, $response) {

    }
}
