<?php

use Symfony\Component\Console\Question\ChoiceQuestion;

class CConsole_View_Component_Choice extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param string                   $question
     * @param array<array-key, string> $choices
     * @param mixed                    $default
     * @param int                      $attempts
     * @param bool                     $multiple
     *
     * @return mixed
     */
    public function render($question, $choices, $default = null, $attempts = null, $multiple = false) {
        return $this->usingQuestionHelper(
            function () use ($question, $choices, $default, $attempts, $multiple) {
                return $this->output->askQuestion(
                    (new ChoiceQuestion($question, $choices, $default))
                        ->setMaxAttempts($attempts)
                        ->setMultiselect($multiple)
                );
            }
        );
    }
}
