<?php

declare(strict_types=1);

namespace League\Flysystem\UrlGeneration;

use League\Flysystem\Config;
use League\Flysystem\UnableToGeneratePublicUrl;

final class ChainedPublicUrlGenerator implements PublicUrlGenerator {
    /**
     * @var iterable
     */
    private $generators;

    /**
     * @param PublicUrlGenerator[] $generators
     */
    public function __construct($generators) {
        $this->generators = $generators;
    }

    /**
     * @param string $path
     * @param Config $config
     *
     * @return string
     */
    public function publicUrl($path, $config) {
        foreach ($this->generators as $generator) {
            try {
                return $generator->publicUrl($path, $config);
            } catch (UnableToGeneratePublicUrl $ex) {
                //do nothing
            }
        }

        throw new UnableToGeneratePublicUrl('No supported public url generator found.', $path);
    }
}
