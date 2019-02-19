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
    public static function repository($options = array()) {
        $defaultOptions = array(
            'driver' => 'array',
        );

        $options = array_merge($defaultOptions, $options);

        $instanceKey = carr::hash($options);
        if (!isset(self::$repository[$instanceKey])) {

            self::$repository[$instanceKey] = new CCache_Repository($options);
        }
        return self::$repository[$instanceKey];
    }

}
