<?php

declare(strict_types=1);

namespace League\Flysystem\Local;

use function in_array;

use League\MimeTypeDetection\MimeTypeDetector;

class FallbackMimeTypeDetector implements MimeTypeDetector {
    private const INCONCLUSIVE_MIME_TYPES = [
        'application/x-empty',
        'text/plain',
        'text/x-asm',
        'application/octet-stream',
        'inode/x-empty',
    ];

    /**
     * @var MimeTypeDetector
     */
    private $detector;

    /**
     * @var array
     */
    private $inconclusiveMimetypes;

    public function __construct(
        MimeTypeDetector $detector,
        array $inconclusiveMimetypes = self::INCONCLUSIVE_MIME_TYPES
    ) {
        $this->detector = $detector;
        $this->inconclusiveMimetypes = $inconclusiveMimetypes;
    }

    public function detectMimeType($path, $contents) {
        return $this->detector->detectMimeType($path, $contents);
    }

    public function detectMimeTypeFromBuffer($contents) {
        return $this->detector->detectMimeTypeFromBuffer($contents);
    }

    public function detectMimeTypeFromPath($path) {
        return $this->detector->detectMimeTypeFromPath($path);
    }

    public function detectMimeTypeFromFile($path) {
        $mimeType = $this->detector->detectMimeTypeFromFile($path);

        if ($mimeType !== null && !in_array($mimeType, $this->inconclusiveMimetypes)) {
            return $mimeType;
        }

        return $this->detector->detectMimeTypeFromPath($path);
    }
}
