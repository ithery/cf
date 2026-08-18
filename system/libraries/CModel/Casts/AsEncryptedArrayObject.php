<?php

class CModel_Casts_AsEncryptedArrayObject implements CModel_Contract_CastableInterface {
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
                if (isset($attributes[$key])) {
                    return new CModel_Casts_ArrayObject(CModel_Casts_Json::decode(CCrypt::encrypter()->decryptString($attributes[$key])));
                }

                return null;
            }

            public function set($model, $key, $value, $attributes) {
                if (!is_null($value)) {
                    return [$key => CCrypt::encrypter()->encryptString(CModel_Casts_Json::encode($value))];
                }

                return null;
            }

            public function serialize($model, string $key, $value, array $attributes) {
                return !is_null($value) ? $value->getArrayCopy() : null;
            }
        };
    }
}
