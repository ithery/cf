<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 9:30:17 AM
 */
interface CDatabase_ResultInterface {
    /**
     * Returns an array containing all of the result set rows.
     *
     * @param int|null   $fetchMode     Controls how the next row will be returned to the caller.
     *                                  The value must be one of the {@link \Doctrine\DBAL\FetchMode} constants,
     *                                  defaulting to {@link \Doctrine\DBAL\FetchMode::MIXED}.
     * @param int|null   $fetchArgument This argument has a different meaning depending on the value of the $fetchMode parameter:
     *                                  * {@link \Doctrine\DBAL\FetchMode::COLUMN}:
     *                                  Returns the indicated 0-indexed column.
     *                                  * {@link \Doctrine\DBAL\FetchMode::CUSTOM_OBJECT}:
     *                                  Returns instances of the specified class, mapping the columns of each row
     *                                  to named properties in the class.
     *                                  * \PDO::FETCH_FUNC: Returns the results of calling the specified function, using each row's
     *                                  columns as parameters in the call.
     * @param array|null $ctorArgs      Controls how the next row will be returned to the caller.
     *                                  The value must be one of the {@link \Doctrine\DBAL\FetchMode} constants,
     *                                  defaulting to {@link \Doctrine\DBAL\FetchMode::MIXED}.
     *
     * @return array
     */
    //public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null);
}
