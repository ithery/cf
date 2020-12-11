<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:06:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCache {

    /**
     *
     * @var CCache_Repository[]
     */
    protected $repository;

    /**
     * 
     * @param array $options
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


        $defaultOptions = array(
            'driver' => 'array',
        );

        return $options ? $options : $defaultOptions;
    }

}
