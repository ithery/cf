<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_HydrationMiddleware_CallPropertyHydrationHooks implements CComponent_HydrationMiddlewareInterface {

    public static function hydrate($instance, $request) {
        $publicProperties = $instance->getPublicPropertiesDefinedBySubClass();

        foreach ($publicProperties as $property => $value) {
            CComponent_Manager::instance()->dispatch('property.hydrate', $property, $value, $instance, $request);

            // Call magic hydrateProperty methods on the component.
            // If the method doesn't exist, the __call with eat it.
            $studlyProperty = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $property)));
            $method = 'hydrate' . $studlyProperty;
            $instance->{$method}($value, $request);

            if (method_exists($instance, $method = 'hydrate' . $studlyProperty)) {
                $instance->{$method}($value, $request);
            }
        }
    }

    public static function dehydrate($instance, $response) {
        $publicProperties = $instance->getPublicPropertiesDefinedBySubClass();

        foreach ($publicProperties as $property => $value) {
            $studlyProperty = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $property)));
            $method = 'dehydrate' . $studlyProperty;
            $instance->{$method}($value, $response);

            CComponent_Manager::instance()->dispatch('property.dehydrate', $property, $value, $instance, $response);
        }
    }

}
