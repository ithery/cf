<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
class CCache {
    /**
     * @var CCache_Repository[]
     */
    protected static $repository;

    /**
     * @param array $options
     *
     * @return CCache_Repository
     */
    public static function repository($options = []) {
        $defaultOptions = [
            'driver' => 'array',
        ];

        $options = array_merge($defaultOptions, $options);

        $instanceKey = carr::hash($options);
        if (!isset(self::$repository[$instanceKey])) {
            self::$repository[$instanceKey] = new CCache_Repository($options);
        }
        return self::$repository[$instanceKey];
    }
}
