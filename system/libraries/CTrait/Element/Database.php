<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 1:19:25 AM
 */
trait CTrait_Element_DataProvider {
    /**
     * @var string|array|closure|CDatabase
     */
    protected $db;

    /**
     * @var CDatabase_Resolver
     */
    protected $dbResolver;

    public function setDatabase($db) {
        $this->db = $db;

        return $this;
    }

    public function setDatabaseResolver(CDatabase_Resolver $dbResolver) {
        $this->dbResolver = $dbResolver;

        return $this;
    }

    public function resolveDatabase() {
        if ($this->dbResolver != null) {
            return $this->dbResolver->connection($this->db);
        }
        if ($this->db instanceof Closure) {
            return call_user_func_array($this->db, []);
        }

        if (is_array($this->db)) {
            return CDatabase::instance(cstr::random(16), $this->db);
        }

        return CDatabase::instance($this->db);
    }
}
