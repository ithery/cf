<?php

class CConsole_View_Component_Mutator_EnsurePunctuation {
    /**
     * Ensures the given string ends with punctuation.
     *
     * @param string $string
     *
     * @return string
     */
    public function __invoke($string) {
        if (!c::str($string)->endsWith(['.', '?', '!', ':'])) {
            return "$string.";
        }

        return $string;
    }
}
