<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 28, 2019, 9:40:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CModel_Search_SearchAspect {

    /**
     * 
     * @return CCollection
     */
    abstract public function getResults($term);

    /**
     * 
     * @return string
     */
    public function getType() {
        if (isset(static::$searchType)) {
            return static::$searchType;
        }
        $className = class_basename(static::class);
        $type = cstr::before($className, 'SearchAspect');
        $type = cstr::snake(cstr::plural($type));
        return cstr::plural($type);
    }

}
