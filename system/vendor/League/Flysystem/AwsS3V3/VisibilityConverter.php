<?php

namespace League\Flysystem\AwsS3V3;

interface VisibilityConverter {
    /**
     * @param string $visibility
     *
     * @return string
     */
    public function visibilityToAcl($visibility);

    /**
     * @param array $grants
     *
     * @return string
     */
    public function aclToVisibility(array $grants);

    /**
     * @return string
     */
    public function defaultForDirectories();
}
