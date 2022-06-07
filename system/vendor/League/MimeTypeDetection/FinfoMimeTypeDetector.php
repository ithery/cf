<?php

namespace League\MimeTypeDetection;

use finfo;

use const FILEINFO_MIME_TYPE;
use const PATHINFO_EXTENSION;

class FinfoMimeTypeDetector implements MimeTypeDetector {
    const INCONCLUSIVE_MIME_TYPES = ['application/x-empty', 'text/plain', 'text/x-asm'];

    /**
     * @var finfo
     */
    private $finfo;

    /**
     * @var ExtensionToMimeTypeMap
     */
    private $extensionMap;

    /**
     * @var null|int
     */
    private $bufferSampleSize;

    /**
     * @param string                      $magicFile
     * @param null|ExtensionToMimeTypeMap $extensionMap
     * @param null|int                    $bufferSampleSize
     */
    public function __construct(
        $magicFile = '',
        ExtensionToMimeTypeMap $extensionMap = null,
        $bufferSampleSize = null
    ) {
        $this->finfo = new finfo(FILEINFO_MIME_TYPE, $magicFile);
        $this->extensionMap = $extensionMap ?: new GeneratedExtensionToMimeTypeMap();
        $this->bufferSampleSize = $bufferSampleSize;
    }

    /**
     * @param string $path
     * @param [type] $contents
     *
     * @return null|string
     */
    public function detectMimeType($path, $contents) {
        $mimeType = is_string($contents)
            ? (@$this->finfo->buffer($this->takeSample($contents)) ?: null)
            : null;

        if ($mimeType !== null && !in_array($mimeType, self::INCONCLUSIVE_MIME_TYPES)) {
            return $mimeType;
        }

        return $this->detectMimeTypeFromPath($path);
    }

    /**
     * @param string $path
     *
     * @return null|string
     */
    public function detectMimeTypeFromPath($path) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return $this->extensionMap->lookupMimeType($extension);
    }

    /**
     * @param string $path
     *
     * @return null|string
     */
    public function detectMimeTypeFromFile($path) {
        return @$this->finfo->file($path) ?: null;
    }

    /**
     * @param string $contents
     *
     * @return null|string
     */
    public function detectMimeTypeFromBuffer($contents) {
        return @$this->finfo->buffer($this->takeSample($contents)) ?: null;
    }

    /**
     * @param string $contents
     *
     * @return string
     */
    private function takeSample($contents) {
        if ($this->bufferSampleSize === null) {
            return $contents;
        }

        return (string) substr($contents, 0, $this->bufferSampleSize);
    }
}
