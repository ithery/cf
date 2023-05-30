<?php

class CDatabase_Connection_MySqlConnection extends CDatabase_Connection {
    /**
     * Escape a binary value for safe SQL embedding.
     *
     * @param string $value
     *
     * @return string
     */
    protected function escapeBinary($value) {
        $hex = bin2hex($value);

        return "x'{$hex}'";
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \CDatabase_Query_Grammar_MySql
     */
    protected function getDefaultQueryGrammar() {
        ($grammar = new CDatabase_Query_Grammar_MySql())->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \CDatabase_Schema_Builder_MySqlBuilder
     */
    public function getSchemaBuilder() {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new CDatabase_Schema_Builder_MySqlBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \CDatabase_Schema_Grammar_MySql
     */
    protected function getDefaultSchemaGrammar() {
        ($grammar = new CDatabase_Schema_Grammar_MySql())->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get the schema state for the connection.
     *
     * @param null|callable $processFactory
     *
     * @return \Illuminate\Database\Schema\MySqlSchemaState
     */
    public function getSchemaState(callable $processFactory = null) {
        //return new MySqlSchemaState($this, $files, $processFactory);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \CDatabase_Query_Processor_MySql
     */
    protected function getDefaultPostProcessor() {
        return new CDatabase_Query_Processor_MySql();
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Illuminate\Database\PDO\MySqlDriver
     */
    protected function getDoctrineDriver() {
        //return new MySqlDriver;
    }
}
