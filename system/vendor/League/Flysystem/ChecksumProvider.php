<?php

namespace League\Flysystem;

interface ChecksumProvider {
    /**
     * @param string $path
     * @param Config $config
     *
     * @throws UnableToProvideChecksum
     * @throws ChecksumAlgoIsNotSupported
     *
     * @return string MD5 hash of the file contents
     */
    public function checksum($path, $config);
}
