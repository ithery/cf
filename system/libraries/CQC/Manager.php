<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 */
class CQC_Manager {
    use CQC_Manager_DatabaseCheckerTrait,
        CQC_Manager_UnitTestTrait;

    protected static $instance;

    protected $testing;

    /**
     * @return CQC_Manager
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        $this->testing = new CQC_Testing();

        if (CFile::isDirectory($unitPath = c::appRoot('default/tests/Unit'))) {
            $this->testing->addSuite($unitPath);
        }
    }

    /**
     * @return CQC_Testing
     */
    public function testing() {
        return $this->testing;
    }
}
