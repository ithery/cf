<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 28, 2019, 9:40:44 PM
 */
abstract class CModel_Search_SearchAspect {
    /**
     * @param mixed $term
     *
     * @return CCollection
     */
    abstract public function getResults($term);

    /**
     * @return string
     */
    public function getType() {
        if (isset(static::$searchType)) {
            return static::$searchType;
        }
        $className = c::classBasename(static::class);
        $type = cstr::before($className, 'SearchAspect');
        $type = cstr::snake(cstr::plural($type));
        return cstr::plural($type);
    }
}
