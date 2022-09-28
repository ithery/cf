<?php

namespace League\Flysystem;

class Filesystem implements FilesystemOperator {
    /**
     * @var FilesystemAdapter
     */
    private $adapter;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PathNormalizer
     */
    private $pathNormalizer;

    public function __construct(
        FilesystemAdapter $adapter,
        array $config = [],
        PathNormalizer $pathNormalizer = null
    ) {
        $this->adapter = $adapter;
        $this->config = new Config($config);
        $this->pathNormalizer = $pathNormalizer ?: new WhitespacePathNormalizer();
    }

    /**
     * @param string $location
     *
     * @return bool
     */
    public function fileExists($location) {
        return $this->adapter->fileExists($this->pathNormalizer->normalizePath($location));
    }

    /**
     * @param string $location
     *
     * @return bool
     */
    public function directoryExists($location) {
        return $this->adapter->directoryExists($this->pathNormalizer->normalizePath($location));
    }

    /**
     * @param string $location
     *
     * @return bool
     */
    public function has($location) {
        $path = $this->pathNormalizer->normalizePath($location);

        return $this->adapter->fileExists($path) || $this->adapter->directoryExists($path);
    }

    /**
     * @param string $location
     * @param string $contents
     * @param array  $config
     *
     * @return void
     */
    public function write($location, $contents, array $config = []) {
        $this->adapter->write(
            $this->pathNormalizer->normalizePath($location),
            $contents,
            $this->config->extend($config)
        );
    }

    /**
     * @param string          $location
     * @param string|resource $contents
     * @param array           $config
     *
     * @return void
     */
    public function writeStream($location, $contents, array $config = []) {
        /* @var resource $contents */
        $this->assertIsResource($contents);
        $this->rewindStream($contents);
        $this->adapter->writeStream(
            $this->pathNormalizer->normalizePath($location),
            $contents,
            $this->config->extend($config)
        );
    }

    /**
     * @param string $location
     *
     * @return string
     */
    public function read($location) {
        return $this->adapter->read($this->pathNormalizer->normalizePath($location));
    }

    /**
     * @param string $location
     *
     * @return void
     */
    public function readStream($location) {
        return $this->adapter->readStream($this->pathNormalizer->normalizePath($location));
    }

    /**
     * @param string $location
     *
     * @return void
     */
    public function delete($location) {
        $this->adapter->delete($this->pathNormalizer->normalizePath($location));
    }

    /**
     * @param string $location
     *
     * @return void
     */
    public function deleteDirectory($location) {
        $this->adapter->deleteDirectory($this->pathNormalizer->normalizePath($location));
    }

    /**
     * @param string $location
     * @param array  $config
     *
     * @return void
     */
    public function createDirectory($location, array $config = []) {
        $this->adapter->createDirectory(
            $this->pathNormalizer->normalizePath($location),
            $this->config->extend($config)
        );
    }

    /**
     * @param string $location
     * @param bool   $deep
     *
     * @return DirectoryListing
     */
    public function listContents($location, $deep = self::LIST_SHALLOW) {
        $path = $this->pathNormalizer->normalizePath($location);

        return new DirectoryListing($this->adapter->listContents($path, $deep));
    }

    /**
     * @param string $source
     * @param string $destination
     * @param array  $config
     *
     * @return void
     */
    public function move($source, $destination, array $config = []) {
        $this->adapter->move(
            $this->pathNormalizer->normalizePath($source),
            $this->pathNormalizer->normalizePath($destination),
            $this->config->extend($config)
        );
    }

    /**
     * @param string $source
     * @param string $destination
     * @param array  $config
     *
     * @return void
     */
    public function copy($source, $destination, array $config = []) {
        $this->adapter->copy(
            $this->pathNormalizer->normalizePath($source),
            $this->pathNormalizer->normalizePath($destination),
            $this->config->extend($config)
        );
    }

    /**
     * @param string $path
     *
     * @return int
     */
    public function lastModified($path) {
        return $this->adapter->lastModified($this->pathNormalizer->normalizePath($path))->lastModified();
    }

    /**
     * @param string $path
     *
     * @return int
     */
    public function fileSize($path) {
        return $this->adapter->fileSize($this->pathNormalizer->normalizePath($path))->fileSize();
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function mimeType($path) {
        return $this->adapter->mimeType($this->pathNormalizer->normalizePath($path))->mimeType();
    }

    /**
     * @param string $path
     * @param string $visibility
     *
     * @return void
     */
    public function setVisibility($path, $visibility) {
        $this->adapter->setVisibility($this->pathNormalizer->normalizePath($path), $visibility);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function visibility($path) {
        return $this->adapter->visibility($this->pathNormalizer->normalizePath($path))->visibility();
    }

    /**
     * @param mixed $contents
     *
     * @return void
     */
    private function assertIsResource($contents) {
        if (is_resource($contents) === false) {
            throw new InvalidStreamProvided(
                'Invalid stream provided, expected stream resource, received ' . gettype($contents)
            );
        } elseif ($type = get_resource_type($contents) !== 'stream') {
            throw new InvalidStreamProvided(
                'Invalid stream provided, expected stream resource, received resource of type ' . $type
            );
        }
    }

    /**
     * @param resource $resource
     *
     * @return void
     */
    private function rewindStream($resource) {
        if (ftell($resource) !== 0 && stream_get_meta_data($resource)['seekable']) {
            rewind($resource);
        }
    }
}
