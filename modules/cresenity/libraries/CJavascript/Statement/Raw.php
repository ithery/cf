<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 3, 2018, 2:05:07 AM
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
