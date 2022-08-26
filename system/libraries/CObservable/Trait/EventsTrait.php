<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 5:12:31 PM
 */
trait CObservable_Trait_EventsTrait {
    public function onClick(Closure $event = null, $options = []) {
        /** @var CObservable $this */
        $compiledJs = '';
        if ($event instanceof Closure) {
            $this->javascript()->startDeferred();
            $event($this->javascript());
            $compiledJs = $this->javascript()->endDeferred();
        }
        $this->javascript->jquery()->onClick($compiledJs);

        return $this;
    }

    public function onChange(Closure $event, $options = []) {
        /** @var CObservable $this */
        $compiledJs = '';
        if ($event instanceof Closure) {
            $this->javascript()->startDeferred();
            $event($this->javascript());
            $compiledJs = $this->javascript()->endDeferred();
        }

        $this->javascript()->jquery()->onChange($compiledJs);

        return $this;
    }
}
