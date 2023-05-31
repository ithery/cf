<?php

class CDatabase_Connection_Pdo_MySqlConnection extends CDatabase_Connection {
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
     * Determine if the connected database is a MariaDB database.
     *
     * @return bool
     */
    public function isMaria() {
        return cstr::contains($this->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), 'MariaDB');
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \CDatabase_Query_Grammar_MySqlGrammar
     */
    protected function getDefaultQueryGrammar() {
        ($grammar = new CDatabase_Query_Grammar_MySqlGrammar())->setConnection($this);

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
     * @return \CDatabase_Schema_Grammar_MySqlGrammar
     */
    protected function getDefaultSchemaGrammar() {
        ($grammar = new CDatabase_Schema_Grammar_MySqlGrammar())->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get the schema state for the connection.
     *
     * @param null|callable $processFactory
     *
     * @return \CDatabase_Schema_SchemaState_MySqlSchemaState
     */
    public function getSchemaState(callable $processFactory = null) {
        return new CDatabase_Schema_SchemaState_MySqlSchemaState($this, $processFactory);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \CDatabase_Query_Processor_MySqlProcessor
     */
    protected function getDefaultPostProcessor() {
        return new CDatabase_Query_Processor_MySqlProcessor();
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \CDatabase_Doctrine_Driver_MySqlDriver
     */
    protected function getDoctrineDriver() {
        return new CDatabase_Doctrine_Driver_MySqlDriver();
    }
}
