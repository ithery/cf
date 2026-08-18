<?php

class CModel_Casts_AsCollection implements CModel_Contract_CastableInterface {
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param array $arguments
     *
     * @return object|string
     */
    public static function castUsing(array $arguments) {
        return new class($arguments) implements CModel_Contract_CastsAttributesInterface {
            protected $arguments;

            public function __construct(array $arguments) {
                $this->arguments = $arguments;
            }

            public function get($model, $key, $value, $attributes) {
                if (!isset($attributes[$key])) {
                    return;
                }
                $data = CModel_Casts_Json::decode($attributes[$key]);

                $collectionClass = $this->arguments[0] ?? CCollection::class;
                if (!is_a($collectionClass, CCollection::class, true)) {
                    throw new InvalidArgumentException('The provided class must extend [' . CCollection::class . '].');
                }

                return is_array($data) ? new $collectionClass($data) : null;
            }

            public function set($model, $key, $value, $attributes) {
                return [$key => CModel_Casts_Json::encode($value)];
            }
        };
    }
}
