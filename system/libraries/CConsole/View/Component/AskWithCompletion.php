<?php

use Symfony\Component\Console\Question\Question;

class CConsole_View_Component_AskWithCompletion extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param string         $question
     * @param array|callable $choices
     * @param string         $default
     *
     * @return mixed
     */
    public function render($question, $choices, $default = null) {
        $question = new Question($question, $default);

        is_callable($choices)
            ? $question->setAutocompleterCallback($choices)
            : $question->setAutocompleterValues($choices);

        return $this->usingQuestionHelper(
            function () use ($question) {
                return $this->output->askQuestion($question);
            }
        );
    }
}
