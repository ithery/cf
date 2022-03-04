<?php

class CModel_Casts_AsArrayObject implements CModel_Contract_CastableInterface {
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param array $arguments
     *
     * @return object|string
     */
    public static function castUsing(array $arguments) {
        return new class() implements CModel_Contract_CastsAttributesInterface {
            public function get($model, $key, $value, $attributes) {
                return isset($attributes[$key]) ? new ArrayObject(json_decode($attributes[$key], true)) : null;
            }

            public function set($model, $key, $value, $attributes) {
                return [$key => json_encode($value)];
            }

            public function serialize($model, $key, $value, array $attributes) {
                return $value->getArrayCopy();
            }
        };
    }
}
