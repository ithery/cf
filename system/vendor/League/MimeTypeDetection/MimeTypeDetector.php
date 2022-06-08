<?php

namespace League\MimeTypeDetection;

interface MimeTypeDetector {
    /**
     * @param string          $path
     * @param string|resource $contents
     *
     * @return null|string
     */
    public function detectMimeType($path, $contents);

    /**
     * @param string $contents
     *
     * @return null|string
     */
    public function detectMimeTypeFromBuffer($contents);

    /**
     * @param string $path
     *
     * @return null|string
     */
    public function detectMimeTypeFromPath($path);

    /**
     * @param string $path
     *
     * @return null|string
     */
    public function detectMimeTypeFromFile($path);
}
