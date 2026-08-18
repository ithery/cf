<?php

class CConsole_View_Component_Ask extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param string $question
     * @param string $default
     *
     * @return mixed
     */
    public function render($question, $default = null) {
        return $this->usingQuestionHelper(function () use ($question, $default) {
            return $this->output->ask($question, $default);
        });
    }
}
