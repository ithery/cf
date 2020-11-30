<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_HydrationMiddleware_PerformActionCalls implements CComponent_HydrationMiddlewareInterface {

    public static function hydrate($unHydratedInstance, $request) {
        try {
            foreach ($request->updates as $update) {
                if ($update['type'] !== 'callMethod')
                    continue;

                $method = $update['payload']['method'];
                $params = $update['payload']['params'];

                $unHydratedInstance->callMethod($method, $params);
            }
        } catch (CValidation_Exception $e) {
            CComponent_Manager::instance()->dispatch('failed-validation', $e->validator);

            $unHydratedInstance->setErrorBag($e->validator->errors());
        }
    }

    public static function dehydrate($instance, $response) {
        //
    }

}
