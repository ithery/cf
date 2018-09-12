<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 12, 2018, 3:40:11 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJob_Event extends CEvent {

    const onJobPreRun = 'CJob_Event_onJobPreRun';
    const onJobPostRun = 'CJob_Event_onJobPostRun';
    const onBackgroundJobPreRun = 'CJob_Event_onBackgroundJobPreRun';
    const onBackgroundJobPostRun = 'CJob_Event_onBackgroundJobPostRun';

    protected $dispatcher;
    protected static $instace;

    protected function __construct() {
        $this->dispatcher = new CJob_Event_Dispatcher();
    }

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CJob_Event();
        }
        return self::$instance;
    }

}
