<?php

/**
 * 
 */
trait CModel_Activity_ActivityTrait {

    public static function bootActivityTrait() {
        static::observe(new CModel_Activity_Observer());
    }

}
