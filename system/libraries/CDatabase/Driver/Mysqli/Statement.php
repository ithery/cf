<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 30, 2019, 7:52:15 PM
 */

/**
 * MySQLi Prepared Statement (experimental).
 */
class CDatabase_Driver_Mysqli_Statement {
    protected $link = null;

    protected $stmt;

    protected $varNames = [];

    protected $varValues = [];

    public function __construct($sql, $link) {
        $this->link = $link;

        $this->stmt = $this->link->prepare($sql);

        return $this;
    }

    public function __destruct() {
        $this->stmt->close();
    }

    /**
     * Sets the bind parameters.
     *
     * @param mixed $paramTypes
     * @param mixed $params
     *
     * @return CDatabase_Driver_Mysqli_Statement
     */
    public function bindParams($paramTypes, $params) {
        $this->varNames = array_keys($params);
        $this->varValues = array_values($params);
        call_user_func_array([$this->stmt, 'bind_param'], array_merge($paramTypes, $this->varNames));

        return $this;
    }

    public function bindResult($params) {
        call_user_func_array([$this->stmt, 'bind_result'], $params);
    }

    /**
     * Runs the statement.
     *
     * @return void
     */
    public function execute() {
        foreach ($this->varNames as $key => $name) {
            $$name = $this->varValues[$key];
        }
        $this->stmt->execute();

        return $this->stmt;
    }
}
