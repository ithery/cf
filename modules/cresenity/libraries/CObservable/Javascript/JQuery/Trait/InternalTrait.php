<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 10:41:50 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Javascript_JQuery_Trait_InternalTrait {

    public function bindVar($var, $value) {
        $this->getObject()->bindVar($var, $value);
        $this->resetObject();
        return $this;
    }

}
