<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 3, 2019, 1:43:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CGit_Model_Commit_Author extends CGit_ModelAbstract {

    protected $name;
    protected $email;

    public function __construct($name, $email) {
        $this->setName($name);
        $this->setEmail($email);
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

}
