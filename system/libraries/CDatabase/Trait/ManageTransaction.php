<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CDatabase
 * @since Jun 30, 2019, 7:10:21 PM
 */
trait CDatabase_Trait_ManageTransaction {
    protected $isSavePoint = false;

    /**
     * Execute a Closure within a transaction.
     *
     * @param \Closure $callback
     * @param int      $attempts
     *
     * @throws \Exception|\Throwable
     *
     * @return mixed
     */
    public function transaction(Closure $callback, $attempts = 1) {
        for ($currentAttempt = 1; $currentAttempt <= $attempts; $currentAttempt++) {
            $this->beginTransaction();
            // We'll simply execute the given callback within a try / catch block and if we
            // catch any exception we can rollback this transaction so that none of this
            // gets actually persisted to a database or stored in a permanent fashion.
            try {
                $callbackResult = $callback($this);
            } catch (Exception $e) {
                // If we catch an exception we'll rollback this transaction and try again if we
                // are not out of attempts. If we are out of attempts we will just throw the
                // exception back out and let the developer handle an uncaught exceptions.
                $this->handleTransactionException(
                    $e,
                    $currentAttempt,
                    $attempts
                );
            } catch (Throwable $e) {
                $this->rollBack();

                throw $e;
            }

            try {
                if ($this->transactions == 1) {
                    $this->commit();
                }

                $this->transactions = max(0, $this->transactions - 1);

                if ($this->transactions == 0) {
                    if ($this->transactionManager) {
                        $this->transactionManager->commit($this->getName());
                    }
                }
            } catch (Exception $e) {
                $this->handleCommitTransactionException(
                    $e,
                    $currentAttempt,
                    $attempts
                );

                continue;
            } catch (Throwable $e) {
                $this->handleCommitTransactionException(
                    $e,
                    $currentAttempt,
                    $attempts
                );

                continue;
            }

            $this->fireConnectionEvent('committed');

            return $callbackResult;
        }
    }

    /**
     * Handle an exception encountered when running a transacted statement.
     *
     * @param \Exception $e
     * @param int        $currentAttempt
     * @param int        $maxAttempts
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function handleTransactionException($e, $currentAttempt, $maxAttempts) {
        // On a deadlock, MySQL rolls back the entire transaction so we can't just
        // retry the query. We have to throw this exception all the way out and
        // let the developer handle it in another way. We will decrement too.
        if ($this->causedByDeadlock($e)
            && $this->transactions > 1
        ) {
            $this->transactions--;

            throw $e;
        }
        // If there was an exception we will rollback this transaction and then we
        // can check if we have exceeded the maximum attempt count for this and
        // if we haven't we will return and try this query again in our loop.
        $this->rollBack();
        if ($this->causedByDeadlock($e) && $currentAttempt < $maxAttempts) {
            return;
        }

        throw $e;
    }

    /**
     * Start a new database transaction.
     *
     * @param bool $isSavePoint
     *
     * @throws \Exception
     *
     * @return void
     */
    public function beginTransaction($isSavePoint = true) {
        $this->createTransaction();

        $this->transactions++;

        if ($this->transactionManager) {
            $this->transactionManager->begin(
                $this->getName(),
                $this->transactions
            );
        }

        $this->fireConnectionEvent('beganTransaction');
    }

    /**
     * Alias of beginTransaction but will not create save point.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function begin() {
        return $this->beginTransaction(false);
    }

    /**
     * Create a transaction within the database.
     *
     * @return void
     */
    protected function createTransaction() {
        if ($this->transactions == 0) {
            $this->reconnectIfMissingConnection();

            try {
                $this->driver->beginTransaction();
            } catch (Throwable $e) {
                $this->handleBeginTransactionException($e);
            }
        } elseif ($this->transactions >= 1 && $this->getQueryGrammar()->supportsSavepoints()) {
            $this->createSavepoint();
        }
    }

    /**
     * Create a save point within the database.
     *
     * @return void
     */
    protected function createSavepoint() {
        $this->driver->query(
            $this->getQueryGrammar()->compileSavepoint('trans' . ($this->transactions + 1))
        );
    }

    /**
     * Handle an exception from a transaction beginning.
     *
     * @param \Throwable $e
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function handleBeginTransactionException($e) {
        if ($this->causedByLostConnection($e)) {
            $this->reconnect();
            $this->driver->beginTransaction();
        } else {
            throw $e;
        }
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     */
    public function commit() {
        if ($this->transactions == 1) {
            $this->driver->commit();
        }
        $this->transactions = max(0, $this->transactions - 1);

        if ($this->transactions == 0) {
            if ($this->transactionManager) {
                $this->transactionManager->commit($this->getName());
            }
        }
        $this->fireConnectionEvent('committed');
    }

    /**
     * Handle an exception encountered when committing a transaction.
     *
     * @param \Throwable $e
     * @param int        $currentAttempt
     * @param int        $maxAttempts
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function handleCommitTransactionException($e, $currentAttempt, $maxAttempts) {
        $this->transactions = max(0, $this->transactions - 1);

        if ($this->causedByConcurrencyError($e)
            && $currentAttempt < $maxAttempts
        ) {
            return;
        }

        if ($this->causedByLostConnection($e)) {
            $this->transactions = 0;
        }

        throw $e;
    }

    /**
     * Rollback the active database transaction.
     *
     * @param null|int $toLevel
     *
     * @throws \Exception
     *
     * @return void
     */
    public function rollback($toLevel = null) {
        // We allow developers to rollback to a certain transaction level. We will verify
        // that this given transaction level is valid before attempting to rollback to
        // that level. If it's not we will just return out and not attempt anything.
        $toLevel = is_null($toLevel)
                    ? $this->transactions - 1
                    : $toLevel;

        if ($toLevel < 0 || $toLevel >= $this->transactions) {
            return;
        }
        // Next, we will actually perform this rollback within this database and fire the
        // rollback event. We will also set the current transaction level to the given
        // level that was passed into this method so it will be right from here out.
        try {
            $this->performRollBack($toLevel);
        } catch (Throwable $e) {
            $this->handleRollBackException($e);
        }

        $this->transactions = $toLevel;

        if ($this->transactionManager) {
            $this->transactionManager->rollback(
                $this->getName(),
                $this->transactions
            );
        }

        $this->fireConnectionEvent('rollingBack');
    }

    /**
     * Perform a rollback within the database.
     *
     * @param int $toLevel
     *
     * @return void
     */
    protected function performRollBack($toLevel) {
        /** @var CDatabase $this */
        if ($toLevel == 0) {
            $this->driver->rollBack();
        } elseif ($this->getQueryGrammar()->supportsSavepoints()) {
            $this->driver->query(
                $this->getQueryGrammar()->compileSavepointRollBack('trans' . ($toLevel + 1))
            );
        }
    }

    /**
     * Handle an exception from a rollback.
     *
     * @param \Exception $e
     *
     * @throws \Exception
     */
    protected function handleRollBackException($e) {
        if ($this->causedByLostConnection($e)) {
            $this->transactions = 0;
            if ($this->transactionManager) {
                $this->transactionManager->rollback(
                    $this->getName(),
                    $this->transactions
                );
            }
        }

        throw $e;
    }

    /**
     * Get the number of active transactions.
     *
     * @return int
     */
    public function transactionLevel() {
        return $this->transactions;
    }

    /**
     * @return bool
     */
    public function inTransaction() {
        return $this->transactions > 0;
    }

    /**
     * Execute the callback after a transaction commits.
     *
     * @param callable $callback
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function afterCommit($callback) {
        if ($this->transactionsManager) {
            return $this->transactionsManager->addCallback($callback);
        }

        throw new RuntimeException('Transactions Manager has not been set.');
    }
}
