<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Oct 7, 2020 
 * @license Ittron Global Teknologi
 */
class CQC_Manager {

    use CQC_Manager_DatabaseCheckerTrait,
        CQC_Manager_UnitTestTrait;

    protected static $instance;

    /**
     * 
     * @return CManager_Daemon
     */
    public static function instance() {

        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
