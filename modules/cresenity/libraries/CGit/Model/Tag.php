<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 3, 2019, 1:42:33 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CGit_Model_Tag extends CGit_Model_GitObject {

    protected $name;

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function isTag() {
        return true;
    }

}
