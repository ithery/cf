<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_PseudoListener extends CObservable_ListenerAbstract {
    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);

        $handlersScript = '';
        foreach ($this->handlers as $handler) {
            $handlersScript .= $handler->js();
        }
        $eventParameterImploded = implode(',', $this->eventParameters);

        $startScript = 'function(' . $eventParameterImploded . ') {';
        $endScript = '}';
        $compiledJs = $startScript . $handlersScript . $endScript;
        $js->append($compiledJs);

        return $js->text();
    }
}
