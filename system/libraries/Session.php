<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Session extends CSession {


    /**
     * Singleton instance of Session.
     */
    public static function instance() {
        //cdbg::deprecated('Deprecated when calling Session Object, Please use CSession');
        return CSession::instance();
    }


}

// End Session Class
