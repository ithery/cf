<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Dec 26, 2017, 4:01:45 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Carbon\Carbon as BaseCarbon;

class CCarbon extends BaseCarbon implements JsonSerializable {

    /**
     * The custom Carbon JSON serializer.
     *
     * @var callable|null
     */
    protected static $serializer;

    /**
     * Prepare the object for JSON serialization.
     *
     * @return array|string
     */
    public function jsonSerialize() {
        if (static::$serializer) {
            return call_user_func(static::$serializer, $this);
        }

        $carbon = $this;

        return call_user_func(function () use ($carbon) {
            return get_object_vars($carbon);
        });
    }

    /**
     * JSON serialize all Carbon instances using the given callback.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function serializeUsing($callback) {
        static::$serializer = $callback;
    }

}
