<?php

namespace League\Flysystem;

class WhitespacePathNormalizer implements PathNormalizer {
    /**
     * @param string $path
     *
     * @return string
     */
    public function normalizePath($path) {
        $path = str_replace('\\', '/', $path);
        $this->rejectFunkyWhiteSpace($path);

        return $this->normalizeRelativePath($path);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    private function rejectFunkyWhiteSpace($path) {
        if (preg_match('#\p{C}+#u', $path)) {
            throw CorruptedPathDetected::forPath($path);
        }
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function normalizeRelativePath($path) {
        $parts = [];

        foreach (explode('/', $path) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;

                case '..':
                    if (empty($parts)) {
                        throw PathTraversalDetected::forPath($path);
                    }
                    array_pop($parts);

                    break;

                default:
                    $parts[] = $part;

                    break;
            }
        }

        return implode('/', $parts);
    }
}
