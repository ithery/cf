<?php
class CDatabase_TransactionManager {
    /**
     * All of the recorded transactions.
     *
     * @var \CCollection
     */
    protected $transactions;

    /**
     * The database transaction that should be ignored by callbacks.
     *
     * @var \CDatabase_TransactionRecord
     */
    protected $callbacksShouldIgnore;

    /**
     * Create a new database transactions manager instance.
     *
     * @return void
     */
    public function __construct() {
        $this->transactions = c::collect();
    }

    /**
     * Start a new database transaction.
     *
     * @param string $connection
     * @param int    $level
     *
     * @return void
     */
    public function begin($connection, $level) {
        $this->transactions->push(
            new CDatabase_TransactionRecord($connection, $level)
        );
    }

    /**
     * Rollback the active database transaction.
     *
     * @param string $connection
     * @param int    $level
     *
     * @return void
     */
    public function rollback($connection, $level) {
        $this->transactions = $this->transactions->reject(function ($transaction) use ($connection, $level) {
            return $transaction->connection == $connection
                   && $transaction->level > $level;
        })->values();
    }

    /**
     * Commit the active database transaction.
     *
     * @param string $connection
     *
     * @return void
     */
    public function commit($connection) {
        list($forThisConnection, $forOtherConnections) = $this->transactions->partition(
            function ($transaction) use ($connection) {
                return $transaction->connection == $connection;
            }
        );

        $this->transactions = $forOtherConnections->values();

        $forThisConnection->map->executeCallbacks();
    }

    /**
     * Register a transaction callback.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function addCallback($callback) {
        if ($current = $this->transactions->last()) {
            return $current->addCallback($callback);
        }

        $callback();
    }

    /**
     * Specify that callbacks should ignore the given transaction when determining if they should be executed.
     *
     * @param \CDatabase_TransactionRecord $transaction
     *
     * @return $this
     */
    public function callbacksShouldIgnore(CDatabase_TransactionRecord $transaction) {
        $this->callbacksShouldIgnore = $transaction;

        return $this;
    }

    /**
     * Get all the transactions.
     *
     * @return \CCollection
     */
    public function getTransactions() {
        return $this->transactions;
    }
}
