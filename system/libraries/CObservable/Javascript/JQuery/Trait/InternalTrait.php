<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CObservable_Javascript_JQuery_Trait_InternalTrait {
    public function bindVar($var, $value) {
        $this->getObject()->bindVar($var, $value);
        $this->resetObject();

        return $this;
    }
}
