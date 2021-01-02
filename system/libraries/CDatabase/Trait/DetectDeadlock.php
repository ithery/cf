<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 30, 2019, 7:34:27 PM
 */
trait CDatabase_Trait_DetectDeadlock {
    /**
     * Determine if the given exception was caused by a deadlock.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    protected function causedByDeadlock(Exception $e) {
        $message = $e->getMessage();
        return cstr::contains($message, [
            'Deadlock found when trying to get lock',
            'deadlock detected',
            'The database file is locked',
            'database is locked',
            'database table is locked',
            'A table in the database is locked',
            'has been chosen as the deadlock victim',
            'Lock wait timeout exceeded; try restarting transaction',
            'WSREP detected deadlock/conflict and aborted the transaction. Try restarting the transaction',
        ]);
    }
}
