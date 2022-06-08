<?php

namespace League\Flysystem;

/**
 * This interface contains everything to read from and inspect
 * a filesystem. All methods containing are non-destructive.
 */
interface FilesystemReader {
    const LIST_SHALLOW = false;

    const LIST_DEEP = true;

    /**
     * @param string $location
     *
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     *
     * @return bool
     */
    public function fileExists($location);

    /**
     * @param string $location
     *
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     *
     * @return bool
     */
    public function directoryExists($location);

    /**
     * @param string $location
     *
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     *
     * @return bool
     */
    public function has($location);

    /**
     * @param string $location
     *
     * @throws UnableToReadFile
     * @throws FilesystemException
     *
     * @return string
     */
    public function read($location);

    /**
     * @param string $location
     *
     * @throws UnableToReadFile
     * @throws FilesystemException
     *
     * @return resource
     */
    public function readStream($location);

    /**
     * @param string $location
     * @param bool   $deep
     *
     * @throws FilesystemException
     *
     * @return DirectoryListing<StorageAttributes>
     */
    public function listContents($location, $deep = self::LIST_SHALLOW);

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     * @return int
     */
    public function lastModified($path);

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     *  @return
     */
    public function fileSize($path);

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     * @return string
     */
    public function mimeType($path);

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     * @return string
     */
    public function visibility($path);
}
