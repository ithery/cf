<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 5:12:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CRenderable_Observable_Trait_EventsTrait {

    public function onClick(Closure $event, $options = array()) {
        $compiledJs='';
        if ($event instanceof Closure) {
            $this->jquery->startDeferred();
            $event($this->jquery);
            $compiledJs = $this->jquery->endDeferred();
        }
        $this->jquery->onClick($compiledJs);
    }
    
    public function onChange(Closure $event, $options = array()) {
        $compiledJs='';
        if ($event instanceof Closure) {
            $this->jquery->startDeferred();
            $event($this->jquery);
            $compiledJs = $this->jquery->endDeferred();
        }
        $this->jquery->onChange($compiledJs);
    }

}
