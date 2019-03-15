<?php

use CEvent_Dispatcher as Dispatcher;

/**
 * 
 */
trait CModel_Activity_ActivityTrait {

    public static function bootActivityTrait() {
        $this->observe(new CModel_Activity_Observer());
    }

}
