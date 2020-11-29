<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
use Carbon\Carbon;

class CComponent_HydrationMiddleware_HydratePublicProperties implements CComponent_HydrationMiddlewareInterface {

    use CQueue_Trait_SerializesAndRestoresModelIdentifiers;

    public static function hydrate($instance, $request) {
        $publicProperties = carr::get($request->memo, 'data', []);

        $dates = CF::get($request, 'memo.dataMeta.dates', []);
        $collections = CF::get($request, 'memo.dataMeta.collections', []);
        $models = CF::get($request, 'memo.dataMeta.models', []);
        $modelCollections = CF::get($request, 'memo.dataMeta.modelCollections', []);
        $stringables = CF::get($request, 'memo.dataMeta.stringables', []);

        foreach ($publicProperties as $property => $value) {
            if ($type = CF::get($dates, $property)) {
                $types = [
                    'native' => DateTime::class,
                    'carbon' => Carbon::class,
                    'ccarbon' => CCarbon::class,
                ];

                CF::set($instance, $property, new $types[$type]($value));
            } else if (in_array($property, $collections)) {
                CF::set($instance, $property, collect($value));
            } else if ($serialized = CF::get($models, $property)) {
                static::hydrateModel($serialized, $property, $request, $instance);
            } else if ($serialized = CF::get($modelCollections, $property)) {
                static::hydrateModels($serialized, $property, $request, $instance);
            } else if (in_array($property, $stringables)) {
                CF::set($instance, $property, new CBase_String($value));
            } else {
                // If the value is null, don't set it, because all values start off as null and this
                // will prevent Typed properties from wining about being set to null.
                is_null($value) || $instance->$property = $value;
            }
        }
    }

    public static function dehydrate($instance, $response) {
        $publicData = $instance->getPublicPropertiesDefinedBySubClass();

        CF::set($response, 'memo.data', []);
        CF::set($response, 'memo.dataMeta', []);

        array_walk($publicData, function ($value, $key) use ($instance, $response) {
            if (
            // The value is a supported type, set it in the data, if not, throw an exception for the user.
                    is_bool($value) || is_null($value) || is_array($value) || is_numeric($value) || is_string($value)
            ) {
                CF::set($response, 'memo.data.' . $key, $value);
            } else if ($value instanceof CQueue_QueueableEntityInterface) {
                static::dehydrateModel($value, $key, $response, $instance);
            } else if ($value instanceof CQueue_QueueableCollectionInterface) {
                static::dehydrateModels($value, $key, $response, $instance);
            } else if ($value instanceof CCollection) {
                $response->memo['dataMeta']['collections'][] = $key;

                CF::set($response, 'memo.data.' . $key, $value->toArray());
            } else if ($value instanceof DateTime) {
                if ($value instanceof CCarbon) {
                    $response->memo['dataMeta']['dates'][$key] = 'ccarbon';
                } elseif ($value instanceof Carbon) {
                    $response->memo['dataMeta']['dates'][$key] = 'carbon';
                } else {
                    $response->memo['dataMeta']['dates'][$key] = 'native';
                }

                CF::set($response, 'memo.data.' . $key, $value->format(\DateTimeInterface::ISO8601));
            } else if ($value instanceof CBase_String) {
                $response->memo['dataMeta']['stringables'][] = $key;

                CF::set($response, 'memo.data.' . $key, $value->__toString());
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
                CF::set($model, $key, CF::get($dirtyModelData, $key));
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
                    CF::set($models[$index], $key, CF::get($dirtyModelData[$index], $key));
                }
            }
        }

        $instance->$property = $models;
    }

    protected static function dehydrateModel($value, $property, $response, $instance) {
        $serializedModel = $value instanceof CQueue_QueueableEntity && !$value->exists ? ['class' => get_class($value)] : (array) (new static)->getSerializedPropertyValue($value);

        // Deserialize the models into the "meta" bag.
        CF::set($response, 'memo.dataMeta.models.' . $property, $serializedModel);

        $filteredModelData = [];
        if ($rules = $instance->rulesForModel($property)) {
            $keys = $rules->keys()->map(function ($key) use ($instance) {
                return $instance->beforeFirstDot($instance->afterFirstDot($key));
            });

            $fullModelData = $instance->$property->toArray();

            foreach ($keys as $key) {
                CF::set($filteredModelData, $key, CF::get($fullModelData, $key));
            }
        }

        // Only include the allowed data (defined by rules) in the response payload
        CF::set($response, 'memo.data.' . $property, $filteredModelData);
    }

    protected static function dehydrateModels($value, $property, $response, $instance) {
        $serializedModel = (array) (new static)->getSerializedPropertyValue($value);

        // Deserialize the models into the "meta" bag.
        CF::set($response, 'memo.dataMeta.modelCollections.' . $property, $serializedModel);

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
                    CF::set($filteredModelData[$index], $key, CF::get($data, $key));
                }
            }
        }

        // Only include the allowed data (defined by rules) in the response payload
        CF::set($response, 'memo.data.' . $property, $filteredModelData);
    }

}
