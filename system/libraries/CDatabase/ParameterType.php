<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:16:36 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Contains statement parameter types.
 */
final class CDatabase_ParameterType {

    /**
     * Represents the SQL NULL data type.
     *
     * @see \PDO::PARAM_NULL
     */
    const _NULL = \PDO::PARAM_NULL;

    /**
     * Represents the SQL INTEGER data type.
     *
     * @see \PDO::PARAM_INT
     */
    const INTEGER = \PDO::PARAM_INT;

    /**
     * Represents the SQL CHAR, VARCHAR, or other string data type.
     *
     * @see \PDO::PARAM_STR
     */
    const STRING = \PDO::PARAM_STR;

    /**
     * Represents the SQL large object data type.
     *
     * @see \PDO::PARAM_LOB
     */
    const LARGE_OBJECT = PDO::PARAM_LOB;

    /**
     * Represents a boolean data type.
     *
     * @see \PDO::PARAM_BOOL
     */
    const BOOLEAN = PDO::PARAM_BOOL;

    /**
     * Represents a binary string data type.
     */
    const BINARY = 16;

    /**
     * This class cannot be instantiated.
     */
    private function __construct() {
        
    }

}
