<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
use Carbon\Carbon;

class CComponent_HydrationMiddleware_HydratePublicProperties implements CComponent_HydrationMiddlewareInterface {
    use CQueue_Trait_SerializesAndRestoresModelIdentifiers;

    public static function hydrate($instance, $request) {
        $publicProperties = carr::get($request->memo, 'data', []);

        $dates = c::get($request, 'memo.dataMeta.dates', []);
        $collections = c::get($request, 'memo.dataMeta.collections', []);
        $models = c::get($request, 'memo.dataMeta.models', []);
        $modelCollections = c::get($request, 'memo.dataMeta.modelCollections', []);
        $stringables = c::get($request, 'memo.dataMeta.stringables', []);

        foreach ($publicProperties as $property => $value) {
            if ($type = c::get($dates, $property)) {
                $types = [
                    'native' => DateTime::class,
                    'carbon' => Carbon::class,
                    'ccarbon' => CCarbon::class,
                ];

                c::set($instance, $property, new $types[$type]($value));
            } elseif (in_array($property, $collections)) {
                c::set($instance, $property, c::collect($value));
            } elseif ($serialized = c::get($models, $property)) {
                static::hydrateModel($serialized, $property, $request, $instance);
            } elseif ($serialized = c::get($modelCollections, $property)) {
                static::hydrateModels($serialized, $property, $request, $instance);
            } elseif (in_array($property, $stringables)) {
                c::set($instance, $property, new CBase_String($value));
            } else {
                // If the value is null, don't set it, because all values start off as null and this
                // will prevent Typed properties from wining about being set to null.
                is_null($value) || $instance->$property = $value;
            }
        }
    }

    public static function dehydrate($instance, $response) {
        $publicData = $instance->getPublicPropertiesDefinedBySubClass();

        c::set($response, 'memo.data', []);
        c::set($response, 'memo.dataMeta', []);

        array_walk($publicData, function ($value, $key) use ($instance, $response) {
            // The value is a supported type, set it in the data, if not, throw an exception for the user.
            if (is_bool($value)
                || is_null($value)
                || is_array($value)
                || is_numeric($value)
                || is_string($value)
            ) {
                c::set($response, 'memo.data.' . $key, $value);
            } elseif ($value instanceof CQueue_QueueableEntityInterface) {
                static::dehydrateModel($value, $key, $response, $instance);
            } elseif ($value instanceof CQueue_QueueableCollectionInterface) {
                static::dehydrateModels($value, $key, $response, $instance);
            } elseif ($value instanceof CCollection) {
                $response->memo['dataMeta']['collections'][] = $key;

                c::set($response, 'memo.data.' . $key, $value->toArray());
            } elseif ($value instanceof DateTime) {
                if ($value instanceof CCarbon) {
                    $response->memo['dataMeta']['dates'][$key] = 'ccarbon';
                } elseif ($value instanceof Carbon) {
                    $response->memo['dataMeta']['dates'][$key] = 'carbon';
                } else {
                    $response->memo['dataMeta']['dates'][$key] = 'native';
                }

                c::set($response, 'memo.data.' . $key, $value->format(\DateTimeInterface::ISO8601));
            } elseif ($value instanceof CBase_String) {
                $response->memo['dataMeta']['stringables'][] = $key;

                c::set($response, 'memo.data.' . $key, $value->__toString());
            } else {
                throw new CComponent_Exception_PublicPropertyTypeNotAllowedException($instance::getName(), $key, $value);
            }
        });
    }

    protected static function hydrateModel($serialized, $property, $request, $instance) {
        if (isset($serialized['id'])) {
            $model = (new static)->getRestoredPropertyValue(
                new CModel_Identifier($serialized['class'], $serialized['id'], $serialized['relations'], $serialized['connection'])
            );
        } else {
            $model = new $serialized['class'];
        }

        $dirtyModelData = $request->memo['data'][$property];

        if ($rules = $instance->rulesForModel($property)) {
            $keys = $rules->keys()->map(function ($key) use ($instance) {
                return $instance->beforeFirstDot($instance->afterFirstDot($key));
            });

            foreach ($keys as $key) {
                c::set($model, $key, c::get($dirtyModelData, $key));
            }
        }

        $instance->$property = $model;
    }

    protected static function hydrateModels($serialized, $property, $request, $instance) {
        $idsWithNullsIntersparsed = $serialized['id'];

        $models = (new static)->getRestoredPropertyValue(
            new CModel_Identifier($serialized['class'], $serialized['id'], $serialized['relations'], $serialized['connection'])
        );

        $dirtyModelData = $request->memo['data'][$property];

        foreach ($idsWithNullsIntersparsed as $index => $id) {
            if ($rules = $instance->rulesForModel($property)) {
                $keys = $rules->keys()
                    ->map([$instance, 'ruleWithNumbersReplacedByStars'])
                    ->mapInto(CBase_String::class)
                    ->filter->contains('*.')
                    ->map->after('*.')
                    ->map->__toString();

                if (is_null($id)) {
                    $model = new $serialized['class'];
                    $models->splice($index, 0, [$model]);
                }

                foreach ($keys as $key) {
                    c::set($models[$index], $key, c::get($dirtyModelData[$index], $key));
                }
            }
        }

        $instance->$property = $models;
    }

    protected static function dehydrateModel($value, $property, $response, $instance) {
        $serializedModel = $value instanceof CQueue_QueueableEntityInterface && !$value->exists ? ['class' => get_class($value)] : (array) (new static)->getSerializedPropertyValue($value);

        // Deserialize the models into the "meta" bag.
        c::set($response, 'memo.dataMeta.models.' . $property, $serializedModel);

        $filteredModelData = [];
        if ($rules = $instance->rulesForModel($property)) {
            $keys = $rules->keys()->map(function ($key) use ($instance) {
                return $instance->beforeFirstDot($instance->afterFirstDot($key));
            });

            $fullModelData = $instance->$property->toArray();

            foreach ($keys as $key) {
                c::set($filteredModelData, $key, c::get($fullModelData, $key));
            }
        }

        // Only include the allowed data (defined by rules) in the response payload
        c::set($response, 'memo.data.' . $property, $filteredModelData);
    }

    protected static function dehydrateModels($value, $property, $response, $instance) {
        $serializedModel = (array) (new static)->getSerializedPropertyValue($value);

        // Deserialize the models into the "meta" bag.
        c::set($response, 'memo.dataMeta.modelCollections.' . $property, $serializedModel);

        $filteredModelData = [];
        if ($rules = $instance->rulesForModel($property)) {
            $keys = $rules->keys()
                ->map([$instance, 'ruleWithNumbersReplacedByStars'])
                ->mapInto(CBase_String::class)
                ->filter->contains('*.')
                ->map->after('*.')
                ->map->__toString();

            $fullModelData = $instance->$property->map->toArray();

            foreach ($fullModelData as $index => $data) {
                $filteredModelData[$index] = [];

                foreach ($keys as $key) {
                    c::set($filteredModelData[$index], $key, c::get($data, $key));
                }
            }
        }

        // Only include the allowed data (defined by rules) in the response payload
        c::set($response, 'memo.data.' . $property, $filteredModelData);
    }
}
