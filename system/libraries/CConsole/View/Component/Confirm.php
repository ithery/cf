<?php

class CConsole_View_Component_Confirm extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param string $question
     * @param bool   $default
     *
     * @return bool
     */
    public function render($question, $default = false) {
        return $this->usingQuestionHelper(function () use ($question, $default) {
            return $this->output->confirm($question, $default);
        });
    }
}
