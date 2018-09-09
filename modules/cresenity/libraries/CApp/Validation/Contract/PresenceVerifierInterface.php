<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 3:58:17 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CApp_Validation_Contract_PresenceVerifierInterface {

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  string  $value
     * @param  int     $excludeId
     * @param  string  $idColumn
     * @param  array   $extra
     * @return int
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = []);

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  array   $values
     * @param  array   $extra
     * @return int
     */
    public function getMultiCount($collection, $column, array $values, array $extra = []);
}
