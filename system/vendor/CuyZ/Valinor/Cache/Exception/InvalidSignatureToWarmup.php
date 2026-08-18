<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Cache\Exception;

use RuntimeException;
use CuyZ\Valinor\Type\Parser\Exception\InvalidType;

/** @internal */
final class InvalidSignatureToWarmup extends RuntimeException {
    public function __construct(string $signature, InvalidType $exception) {
        parent::__construct(
            "Cannot warm up invalid signature `$signature`: {$exception->getMessage()}",
            1653330261,
            $exception
        );
    }
}
