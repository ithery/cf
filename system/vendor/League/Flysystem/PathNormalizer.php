<?php

namespace League\Flysystem;

interface PathNormalizer {
    /**
     * @param string $path
     *
     * @return string
     */
    public function normalizePath($path);
}
