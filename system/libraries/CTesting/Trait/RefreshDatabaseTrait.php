<?php

trait CTesting_Trait_RefreshDatabaseTrait {
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function refreshDatabase() {
        $this->beforeRefreshingDatabase();

        $this->refreshTestDatabase();

        $this->afterRefreshingDatabase();
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function refreshTestDatabase() {
        $this->beginDatabaseTransaction();
    }

    /**
     * Begin a database transaction on the testing database.
     *
     * @return void
     */
    public function beginDatabaseTransaction() {
        foreach ($this->connectionsToTransact() as $name) {
            $connection = CDatabase::manager()->connection($name);
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->beginTransaction();
            $connection->setEventDispatcher($dispatcher);

            if ($connection->getTransactionManager()) {
                $connection->getTransactionManager()->callbacksShouldIgnore(
                    $connection->getTransactionManager()->getTransactions()->first()
                );
            }
        }

        $this->beforeApplicationDestroyed(function () {
            foreach ($this->connectionsToTransact() as $name) {
                $connection = CDatabase::manager()->connection($name);
                $dispatcher = $connection->getEventDispatcher();

                $connection->unsetEventDispatcher();
                $connection->rollBack();
                $connection->setEventDispatcher($dispatcher);
                // Deliberately not calling $connection->disconnect() here: Laravel's
                // RefreshDatabase can do that safely because every test gets a freshly
                // booted application (a new lazy PDO resolver). CTesting_TestCase reuses
                // one already-booted global CContainer for the whole PHPUnit process, so
                // disconnecting here would null out the shared PDO with nothing left to
                // lazily reconnect it — the next query in any later test would fatal with
                // "Call to a member function quote() on null".
            }
        });
    }

    /**
     * The database connections that should have transactions.
     *
     * @return array
     */
    protected function connectionsToTransact() {
        return property_exists($this, 'connectionsToTransact')
                            ? $this->connectionsToTransact : [null];
    }

    /**
     * Perform any work that should take place before the database has started refreshing.
     *
     * @return void
     */
    protected function beforeRefreshingDatabase() {
        // ...
    }

    /**
     * Perform any work that should take place once the database has finished refreshing.
     *
     * @return void
     */
    protected function afterRefreshingDatabase() {
        // ...
    }
}
