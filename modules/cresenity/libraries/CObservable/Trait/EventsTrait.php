<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 5:12:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Trait_EventsTrait {


    public function onClick(Closure $event=null, $options = array()) {
        $compiledJs = '';
        
        if ($event instanceof Closure) {
            $this->javascript->startDeferred();
            $event($this->javascript);
            $compiledJs = $this->javascript->endDeferred();
        }
        $this->javascript->jquery()->onClick($compiledJs);
    }

    public function onChange(Closure $event, $options = array()) {
        $compiledJs = '';
        if ($event instanceof Closure) {
            $this->javascript->startDeferred();
            $event($this->javascript);
            $compiledJs = $this->javascript->endDeferred();
        }

        $this->javascript->jquery()->onChange($compiledJs);
    }

}
