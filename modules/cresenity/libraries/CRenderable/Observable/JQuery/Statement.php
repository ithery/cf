<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 8:10:42 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CRenderable_Observable_JQuery_Statement {

    protected $statement;

    public function __construct($statement = ';') {
        $this->statement = $statement;
    }

    public function __toString() {
        return $statement;
    }

}
