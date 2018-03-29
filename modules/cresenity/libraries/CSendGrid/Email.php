<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Email
 *
 * @author Hery Kurniawan
 * @since Jan 7, 2018, 12:31:48 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CSendGrid_Email implements \JsonSerializable {

    private $name;
    private $email;

    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function jsonSerialize() {
        return array_filter(
                        [
                    'name' => $this->getName(),
                    'email' => $this->getEmail()
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
