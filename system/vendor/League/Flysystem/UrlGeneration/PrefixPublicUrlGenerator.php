<?php

declare(strict_types=1);

namespace League\Flysystem\UrlGeneration;

use League\Flysystem\Config;
use League\Flysystem\PathPrefixer;

class PrefixPublicUrlGenerator implements PublicUrlGenerator {
    /**
     * @var PathPrefixer
     */
    private $prefixer;

    /**
     * @param string $urlPrefix;
     */
    public function __construct($urlPrefix) {
        $this->prefixer = new PathPrefixer($urlPrefix, '/');
    }

    /**
     * @param string $path
     * @param Config $config
     *
     * @return string
     */
    public function publicUrl($path, $config) {
        return $this->prefixer->prefixPath($path);
    }
}
