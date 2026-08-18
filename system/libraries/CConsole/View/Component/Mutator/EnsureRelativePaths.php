<?php

class CConsole_View_Component_Mutator_EnsureRelativePaths {
    /**
     * Ensures the given string only contains relative paths.
     *
     * @param string $string
     *
     * @return string
     */
    public function __invoke($string) {
        // if (function_exists('app') && app()->has('path.base')) {
        //     $string = str_replace(base_path().'/', '', $string);
        // }
        $string = str_replace(DOCROOT, '', $string);

        return $string;
    }
}
