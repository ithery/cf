<?php

class CConsole_View_Component_Mutator_EnsureNoPunctuation {
    /**
     * Ensures the given string does not end with punctuation.
     *
     * @param string $string
     *
     * @return string
     */
    public function __invoke($string) {
        if (c::str($string)->endsWith(['.', '?', '!', ':'])) {
            return substr_replace($string, '', -1);
        }

        return $string;
    }
}
