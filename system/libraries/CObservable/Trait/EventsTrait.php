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
        $compiledJs = $this->getCompiledEventJs($event);

        $this->javascript()->jquery()->onClick($compiledJs);

        return $this;
    }

    public function onHover(Closure $event = null, $options = []) {
        /** @var CObservable $this */
        $compiledJs = $this->getCompiledEventJs($event);

        $this->javascript()->jquery()->onHover($compiledJs);

        return $this;
    }

    public function onMouseEnter(Closure $event = null, $options = []) {
        /** @var CObservable $this */
        $compiledJs = $this->getCompiledEventJs($event);

        $this->javascript()->jquery()->onMouseEnter($compiledJs);

        return $this;
    }

    public function onMouseLeave(Closure $event = null, $options = []) {
        /** @var CObservable $this */
        $compiledJs = $this->getCompiledEventJs($event);

        $this->javascript()->jquery()->onMouseLeave($compiledJs);

        return $this;
    }

    public function onChange(Closure $event, $options = []) {
        /** @var CObservable $this */
        $compiledJs = $this->getCompiledEventJs($event);
        $this->javascript()->jquery()->onChange($compiledJs);

        return $this;
    }

    private function getCompiledEventJs(Closure $event) {
        return CJavascript::getJsStatementFromClosure($event, [$this->javascript()]);
    }
}
