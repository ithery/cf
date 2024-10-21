<?php

declare(strict_types=1);

namespace League\Flysystem\UrlGeneration;

use League\Flysystem\Config;
use League\Flysystem\UnableToGeneratePublicUrl;

interface PublicUrlGenerator {
    /**
     * @param string $path
     * @param Config $config
     *
     * @throws UnableToGeneratePublicUrl
     */
    public function publicUrl($path, $config);
}
