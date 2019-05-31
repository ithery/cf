<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 17, 2019, 11:30:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_PseudoListener extends CObservable_ListenerAbstract {

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);



        $handlersScript = "";
        foreach ($this->handlers as $handler) {
            $handlersScript .= $handler->js();
        }
        $eventParameterImploded = implode(',', $this->eventParameters);

        $startScript = "function(" . $eventParameterImploded . ") {";
        $endScript = "}";
        $compiledJs = $startScript . $handlersScript . $endScript;
        $js->append($compiledJs);

        return $js->text();
    }

}
