<?php

use Illuminate\Contracts\Support\Arrayable;

class CValidation_Rule_ArrayRule implements Stringable {
    /**
     * The accepted keys.
     *
     * @var array
     */
    protected $keys;

    /**
     * Create a new array rule instance.
     *
     * @param null|array $keys
     *
     * @return void
     */
    public function __construct($keys = null) {
        if ($keys instanceof Arrayable) {
            $keys = $keys->toArray();
        }

        $this->keys = is_array($keys) ? $keys : func_get_args();
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString() {
        if (empty($this->keys)) {
            return 'array';
        }

        $keys = array_map(
            static function ($key) {
                if (class_exists(BackedEnum::class) && $key instanceof BackedEnum) {
                    return $key->value;
                }
                if (class_exists(UnitEnum::class) && $key instanceof UnitEnum) {
                    return $key->value;
                }

                return $key;
            },
            $this->keys,
        );

        return 'array:' . implode(',', $keys);
    }
}
