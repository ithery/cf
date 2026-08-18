<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToGeneratePublicUrl extends RuntimeException implements FilesystemException
{
    /**
     * @param string $reason
     * @param string $path
     * @param Throwable|null $previous
     */
    public function __construct($reason, $path, $previous = null)
    {
        parent::__construct("Unable to generate public url for $path: $reason", 0, $previous);
    }

    /**
     * @param string $path
     * @param Throwable $exception
     * @return static
     */
    public static function dueToError( $path, $exception)
    {
        return new static($exception->getMessage(), $path, $exception);
    }

    /**
     * @param string $path
     * @param string $extraReason
     * @return static
     */
    public static function noGeneratorConfigured($path, $extraReason = '')
    {
        return new static('No generator was configured ' . $extraReason, $path);
    }
}
