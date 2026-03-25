<?php

defined('SYSPATH') or die('No direct access allowed.');

final class CDatabase_TransactionIsolationLevel {
    /**
     * Transaction isolation level READ UNCOMMITTED.
     */
    const READ_UNCOMMITTED = 1;

    /**
     * Transaction isolation level READ COMMITTED.
     */
    const READ_COMMITTED = 2;

    /**
     * Transaction isolation level REPEATABLE READ.
     */
    const REPEATABLE_READ = 3;

    /**
     * Transaction isolation level SERIALIZABLE.
     */
    const SERIALIZABLE = 4;

    private function __construct() {
    }
}
