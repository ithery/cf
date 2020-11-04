<?php

/**
 * Description of UnitTestTrait
 *
 * @author Hery
 */
trait CQC_Manager_UnitTestTrait {

    protected $unitTest = array();
    protected $unitTestGroup = array();

    public function registerUnitTest($class, $name = null, $group = null) {

        if ($name == null) {
            $name = carr::last(explode('_', $class));
        }
        $this->unitTest[$class] = $name;
        if ($group !== null) {
            if (!isset($this->unitTestGroup[$group])) {
                $this->unitTestGroup[$group] = [];
            }
            $this->unitTestGroup[$group][$class] = $name;
        }
    }

    public function unitTests($group = null) {
        if ($group === null) {
            return $this->unitTest;
        }
        if ($group === false) {
            $allDaemons = $this->unitTest;
            foreach ($this->unitTestGroup as $groupArray) {
                $allDaemons = array_diff_key($allDaemons, $groupArray);
            }
            return $allDaemons;
        }
        if ($group !== null) {
            if (!in_array($group, $this->getGroupsKey())) {
                throw new Exception('group daemon ' . $group . ' not available');
            }
        }
        return $this->unitTestGroup[$group];
    }

    public function getUnitTestGroupsKey() {
        return array_keys($this->unitTestGroup);
    }

    public function haveUnitTestGroup() {
        return count($this->getUnitTestGroupsKey()) > 0;
    }

}
