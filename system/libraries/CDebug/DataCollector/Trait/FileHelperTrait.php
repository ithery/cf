<?php

trait CDebug_DataCollector_Trait_FileHelperTrait {
    /**
     * Check if the given file is to be excluded from analysis.
     *
     * @param string $file
     *
     * @return bool
     */
    protected function fileIsInExcludedPath($file) {
        $excludedPaths = [
            '/system/core/',
            '/system/libraries/',
            '/system/vendor/',
        ];
        $normalizedPath = str_replace('\\', '/', $file);
        foreach ($excludedPaths as $excludedPath) {
            if (strpos($normalizedPath, $excludedPath) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Shorten the path by removing the relative links and base dir.
     *
     * @param string $path
     *
     * @return string
     */
    protected function normalizeFilename($path) {
        if (file_exists($path)) {
            $path = realpath($path);
        }

        return str_replace(DOCROOT, '', $path);
    }
}
