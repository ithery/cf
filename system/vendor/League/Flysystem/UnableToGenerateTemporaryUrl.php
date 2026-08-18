<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;
use Throwable;

final class UnableToGenerateTemporaryUrl extends RuntimeException implements FilesystemException
{
    /**
     * @param string $reason
     * @param string $path
     * @param Throwable|null $previous
     */
    public function __construct($reason, $path, $previous = null)
    {
        parent::__construct("Unable to generate temporary url for $path: $reason", 0, $previous);
    }

    /**
     * @param string $path
     * @param Throwable $exception
     * @return static
     */
    public static function dueToError(string $path, Throwable $exception)
    {
        return new static($exception->getMessage(), $path, $exception);
    }

    /**
     * @param string $path
     * @param string $extraReason
     * @return static
     */
    public static function noGeneratorConfigured(string $path, string $extraReason = '')
    {
        return new static('No generator was configured ' . $extraReason, $path);
    }
}
