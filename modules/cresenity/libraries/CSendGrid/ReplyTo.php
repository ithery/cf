<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 7, 2018, 12:26:41 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CSendGrid_ReplyTo implements JsonSerializable {

    private $email;
    private $name;

    public function __construct($email, $name = null) {
        $this->email = $email;

        if (!is_null($name)) {
            $this->name = $name;
        }
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function jsonSerialize() {
        return array_filter(
                        [
                    'email' => $this->getEmail(),
                    'name' => $this->getName(),
                        ], function ($value) {
                    return $value !== null;
                }
                ) ?: null;
    }

}
