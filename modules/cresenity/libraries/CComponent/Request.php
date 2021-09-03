<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_Request {

    public $fingerprint;
    public $updates;
    public $memo;

    public function __construct($payload) {
        $this->fingerprint = $payload['fingerprint'];
        $this->updates = $payload['updates'];
        $this->memo = $payload['serverMemo'];
    }

    public function id() {
        return $this->fingerprint['id'];
    }

    public function name() {
        return $this->fingerprint['name'];
    }

}
