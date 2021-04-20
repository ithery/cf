<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 11:55:45 AM
 */

/**
 * Marker interface for constraints.
 */
interface CDatabase_Schema_Constraint {
    /**
     * @return string
     */
    public function getName();

    /**
     * @param CDatabase_Platform $platform
     *
     * @return string
     */
    public function getQuotedName(CDatabase_Platform $platform);

    /**
     * Returns the names of the referencing table columns
     * the constraint is associated with.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Returns the quoted representation of the column names
     * the constraint is associated with.
     *
     * But only if they were defined with one or a column name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param \CDatabase_Platform $platform the platform to use for quotation
     *
     * @return array
     */
    public function getQuotedColumns(CDatabase_Platform $platform);
}
