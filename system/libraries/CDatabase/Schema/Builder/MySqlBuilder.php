<?php

class CDatabase_Schema_Builder_MySqlBuilder extends CDatabase_Schema_Builder {
    /**
     * Create a database in the schema.
     *
     * @param string $name
     *
     * @return bool
     */
    public function createDatabase($name) {
        return $this->connection->statement(
            $this->grammar->compileCreateDatabase($name, $this->connection)
        );
    }

    /**
     * Drop a database from the schema if the database exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function dropDatabaseIfExists($name) {
        return $this->connection->statement(
            $this->grammar->compileDropDatabaseIfExists($name)
        );
    }

    /**
     * Get the tables for the database.
     *
     * @return array
     */
    public function getTables() {
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        return $this->connection->getPostProcessor()->processTables(
            $this->connection->selectFromWriteConnection(
                $grammar->compileTables($this->connection->getDatabaseName())
            )
        );
    }

    /**
     * Get the views for the database.
     *
     * @return array
     */
    public function getViews() {
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        return $this->connection->getPostProcessor()->processViews(
            $this->connection->selectFromWriteConnection(
                $grammar->compileViews($this->connection->getDatabaseName())
            )
        );
    }

    /**
     * Get the columns for a given table.
     *
     * @param string $table
     *
     * @return array
     */
    public function getColumns($table) {
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        $table = $this->connection->getTablePrefix() . $table;

        $results = $this->connection->selectFromWriteConnection(
            $grammar->compileColumns($this->connection->getDatabaseName(), $table)
        );

        return $this->connection->getPostProcessor()->processColumns($results);
    }

    /**
     * Get the indexes for a given table.
     *
     * @param string $table
     *
     * @return array
     */
    public function getIndexes($table) {
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        $table = $this->connection->getTablePrefix() . $table;

        return $this->connection->getPostProcessor()->processIndexes(
            $this->connection->selectFromWriteConnection(
                $grammar->compileIndexes($this->connection->getDatabaseName(), $table)
            )
        );
    }

    /**
     * Get the foreign keys for a given table.
     *
     * @param string $table
     *
     * @return array
     */
    public function getForeignKeys($table) {
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        $table = $this->connection->getTablePrefix() . $table;

        return $this->connection->getPostProcessor()->processForeignKeys(
            $this->connection->selectFromWriteConnection(
                $grammar->compileForeignKeys($this->connection->getDatabaseName(), $table)
            )
        );
    }

    /**
     * Drop all tables from the database.
     *
     * @return void
     */
    public function dropAllTables() {
        $tables = [];

        foreach ($this->getAllTables() as $row) {
            $row = (array) $row;

            $tables[] = reset($row);
        }

        if (empty($tables)) {
            return;
        }

        $this->disableForeignKeyConstraints();
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        $this->connection->statement(
            $grammar->compileDropAllTables($tables)
        );

        $this->enableForeignKeyConstraints();
    }

    /**
     * Drop all views from the database.
     *
     * @return void
     */
    public function dropAllViews() {
        $views = [];

        foreach ($this->getAllViews() as $row) {
            $row = (array) $row;

            $views[] = reset($row);
        }

        if (empty($views)) {
            return;
        }
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        $this->connection->statement(
            $grammar->compileDropAllViews($views)
        );
    }

    /**
     * Get all of the table names for the database.
     *
     * @return array
     */
    public function getAllTables() {
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        return $this->connection->select(
            $grammar->compileGetAllTables()
        );
    }

    /**
     * Get all of the view names for the database.
     *
     * @return array
     */
    public function getAllViews() {
        $grammar = $this->grammar;
        /** @var CDatabase_Schema_Grammar_MySqlGrammar $grammar */
        return $this->connection->select(
            $grammar->compileGetAllViews()
        );
    }
}
