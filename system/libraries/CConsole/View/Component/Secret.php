<?php

use Symfony\Component\Console\Question\Question;

class CConsole_View_Component_Secret extends CConsole_View_ComponentAbstract {
    /**
     * Renders the component using the given arguments.
     *
     * @param string $question
     * @param bool   $fallback
     *
     * @return mixed
     */
    public function render($question, $fallback = true) {
        $question = new Question($question);

        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->usingQuestionHelper(function () use ($question) {
            return $this->output->askQuestion($question);
        });
    }
}
