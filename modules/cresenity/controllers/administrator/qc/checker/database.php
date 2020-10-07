<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Oct 7, 2020 
 * @license Ittron Global Teknologi
 */


class Controller_Administrator_Qc_Checker_Database extends CApp_Administrator_Controller_User {
    use CTrait_Controller_Application_QC_DatabaseChecker;
}