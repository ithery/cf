<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_Request {
    public $fingerprint;

    public $updates;

    public $memo;

    public function __construct($payload) {
        $this->fingerprint = carr::get($payload, 'fingerprint');
        $this->updates = carr::get($payload, 'updates');
        $this->memo = carr::get($payload, 'serverMemo');
    }

    public function id() {
        return carr::get($this->fingerprint, 'id');
    }

    public function name() {
        return carr::get($this->fingerprint, 'name');
    }
}
