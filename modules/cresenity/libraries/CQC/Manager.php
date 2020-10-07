<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Oct 7, 2020 
 * @license Ittron Global Teknologi
 */

class CQC_Manager {
    protected static $instance;
    protected $databaseChecker = array();
    protected $databaseCheckerGroup = array();

    /**
     * 
     * @return CManager_Daemon
     */
    public static function instance() {

        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    
    public function registerDatabaseChecker($class, $name = null, $group = null) {

        if ($name == null) {
            $name = carr::last(explode('_', $class));
        }
        $this->databaseChecker[$class] = $name;
        if ($group !== null) {
            if (!isset($this->databaseCheckerGroup[$group])) {
                $this->databaseCheckerGroup[$group] = [];
            }
            $this->databaseCheckerGroup[$group][$class] = $name;
        }
    }
    
     public function checkers($group = null) {
        if ($group === null) {
            return $this->databaseChecker;
        }
        if ($group === false) {
            $allDaemons = $this->databaseChecker;
            foreach ($this->databaseCheckerGroup as $groupArray) {
                $allDaemons = array_diff_key($allDaemons, $groupArray);
            }
            return $allDaemons;
        }
        if ($group !== null) {
            if (!in_array($group, $this->getGroupsKey())) {
                throw new Exception('group daemon ' . $group . ' not available');
            }
        }
        return $this->databaseCheckerGroup[$group];
    }
    
    public function getGroupsKey() {
        return array_keys($this->databaseCheckerGroup);
    }

    public function haveGroup() {
        return count($this->getGroupsKey()) > 0;
    }
}