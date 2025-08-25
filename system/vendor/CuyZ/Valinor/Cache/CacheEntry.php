<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Cache;

/** @internal */
final class CacheEntry
{
    public string $code;
    /** @var list<non-empty-string> */
    public array $filesToWatch = [];

    public function __construct(
        string $code,
        /** @var list<non-empty-string> */
        array $filesToWatch = []
    ) {
        $this->code = $code;
        $this->filesToWatch = $filesToWatch;
    }
}
