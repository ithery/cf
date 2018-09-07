<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 3, 2018, 2:05:07 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_Statement_Raw extends CJavascript_Statement {

    protected $raw = '';

    public function __construct($raw = ';') {
        $this->raw = $raw;
    }

    public function setStatement($statement) {
        $this->raw = $statement;
    }

    public function getStatement() {
        return trim($this->raw, ';') . ';';
    }

}
