<?php

trait CTesting_Concern_InteractsWithDatabase {
    /**
     * Assert that a given where condition exists in the database.
     *
     * @param string             $table
     * @param array              $data
     * @param null|string        $connection
     *
     * @return $this
     */
    protected function assertDatabaseHas($table, array $data, $connection = null) {
        $this->assertThat(
            $table,
            new CTesting_Constraint_HasInDatabase($this->getConnection($connection), $data)
        );

        return $this;
    }

    /**
     * Assert that a given where condition does not exist in the database.
     *
     * @param string      $table
     * @param array       $data
     * @param null|string $connection
     *
     * @return $this
     */
    protected function assertDatabaseMissing($table, array $data, $connection = null) {
        $constraint = new CTesting_Constraint_HasInDatabase($this->getConnection($connection), $data);

        $this->assertThat($table, new PHPUnit\Framework\Constraint\Operator\LogicalNot($constraint));

        return $this;
    }

    /**
     * Assert the count of table entries.
     *
     * @param string      $table
     * @param int         $count
     * @param null|string $connection
     *
     * @return $this
     */
    protected function assertDatabaseCount($table, $count, $connection = null) {
        $actual = $this->getConnection($connection)->table($table)->count();

        $this->assertSame(
            $count,
            $actual,
            "Expected {$table} to contain {$count} rows, found {$actual}."
        );

        return $this;
    }

    /**
     * Get the database connection.
     *
     * @param null|string $connection
     *
     * @return \CDatabase_Connection
     */
    protected function getConnection($connection = null) {
        return CDatabase::manager()->connection($connection);
    }
}
