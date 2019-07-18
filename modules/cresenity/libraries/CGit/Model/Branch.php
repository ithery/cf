<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 3, 2019, 1:41:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CGit_Model_Branch extends CGit_ModelAbstract {

    protected $name;

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

}
