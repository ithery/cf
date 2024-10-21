<?php

trait CDatabase_Trait_ParsesSearchPathTrait {
    /**
     * Parse the Postgres "search_path" configuration value into an array.
     *
     * @param null|string|array $searchPath
     *
     * @return array
     */
    protected function parseSearchPath($searchPath) {
        if (is_string($searchPath)) {
            preg_match_all('/[^\s,"\']+/', $searchPath, $matches);

            $searchPath = $matches[0];
        }

        return array_map(function ($schema) {
            return trim($schema, '\'"');
        }, $searchPath ?? []);
    }
}
