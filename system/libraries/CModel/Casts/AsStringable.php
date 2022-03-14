<?php

class CModel_Casts_AsStringable implements CModel_Contract_CastableInterface {
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
                return isset($value) ? cstr::of($value) : null;
            }

            public function set($model, $key, $value, $attributes) {
                return isset($value) ? (string) $value : null;
            }
        };
    }
}
