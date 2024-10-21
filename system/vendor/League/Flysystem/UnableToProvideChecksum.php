<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToProvideChecksum extends RuntimeException implements FilesystemException
{
    /**
     * @param string $reason
     * @param string $path
     * @param Throwable|null $previous
     */
    public function __construct($reason, $path, $previous = null)
    {
        parent::__construct("Unable to get checksum for $path: $reason", 0, $previous);
    }
}
