<?php
class CDatabase_Driver_Pgsql_Statement {
    protected $link = null;

    protected $stmt;

    public function __construct($sql, $link) {
        $this->link = $link;

        $this->stmt = $this->link->prepare($sql);

        return $this;
    }

    public function __destruct() {
        $this->stmt->close();
    }

    /**
     *  Sets the bind parameters
     *
     * @return CDatabase_Driver_Pgsql_Statement
     */
    public function bindParams() {
        $argv = func_get_args();
        return $this;
    }

    //

    /**
     * Sets the statement values to the bound parameters
     *
     * @return CDatabase_Driver_Pgsql_Statement
     */
    public function setVals() {
        return $this;
    }

    /**
     * Runs the statement
     *
     * @return CDatabase_Driver_Pgsql_Statement
     */
    public function execute() {
        return $this;
    }
}
