<?php

/**
 * Representation of a Database View.
 */
class CDatabase_Schema_View extends CDatabase_AbstractAsset {
    /**
     * @var string
     */
    private $sql;

    /**
     * @param string $name
     * @param string $sql
     */
    public function __construct($name, $sql) {
        $this->setName($name);
        $this->sql = $sql;
    }

    /**
     * @return string
     */
    public function getSql() {
        return $this->sql;
    }
}
