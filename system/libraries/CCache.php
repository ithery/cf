<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 1:06:35 PM
 */
class CCache {
    /**
     * @var CCache_Repository[]
     */
    protected $repository;

    /**
     * @param null|mixed $name
     *
     * @return CCache_Repository
     */
    public static function repository($name = null) {
        $options = static::resolveConfig($name);
        $instanceKey = $name;
        if (!is_string($name)) {
            $instanceKey = carr::hash($options);
        }
        if (!isset(self::$repository[$instanceKey])) {
            self::$repository[$instanceKey] = new CCache_Repository($options);
        }
        return self::$repository[$instanceKey];
    }

    public static function resolveConfig($name) {
        $config = CConfig::instance('cache');
        $options = null;
        if (is_array($name)) {
            $options = $name;
        }
        if ($options == null) {
            if ($name = null) {
                $name = $config->get('default');
            }

            $options = $config->get($name);
        }

        $defaultOptions = [
            'driver' => 'array',
        ];

        return $options ? $options : $defaultOptions;
    }
}
