<?php

namespace League\Flysystem;

interface FilesystemAdapter {
    /**
     * @param string $path
     *
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     */
    public function fileExists($path);

    /**
     * @param string $path
     *
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     */
    public function directoryExists($path);

    /**
     * @param string $path
     * @param string $contents
     *
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function write($path, $contents, Config $config);

    /**
     * @param resource $contents
     * @param string   $path
     *
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function writeStream($path, $contents, Config $config);

    /**
     * @param string $path
     *
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function read($path);

    /**
     * @param string $path
     *
     * @throws UnableToReadFile
     * @throws FilesystemException
     *
     * @return resource
     */
    public function readStream($path);

    /**
     * @param string $path
     *
     * @throws UnableToDeleteFile
     * @throws FilesystemException
     */
    public function delete($path);

    /**
     * @param string $path
     *
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     */
    public function deleteDirectory($path);

    /**
     * @param string $path
     *
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     */
    public function createDirectory($path, Config $config);

    /**
     * @param string $path
     * @param string $visibility
     *
     * @throws InvalidVisibilityProvided
     * @throws FilesystemException
     */
    public function setVisibility($path, $visibility);

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     * @return FileAttributes
     */
    public function visibility($path);

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     * @return FileAttributes
     */
    public function mimeType($path);

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     * @return FileAttributes
     */
    public function lastModified($path);

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     * @return FileAttributes
     */
    public function fileSize($path);

    /**
     * @param string $path
     * @param bool   $deep
     *
     * @throws FilesystemException
     *
     * @return iterable<StorageAttributes>
     */
    public function listContents($path, $deep);

    /**
     * @param string $source
     * @param string $destination
     *
     * @throws UnableToMoveFile
     * @throws FilesystemException
     */
    public function move($source, $destination, Config $config);

    /**
     * @param string $source
     * @param string $destination
     *
     * @throws UnableToCopyFile
     * @throws FilesystemException
     */
    public function copy($source, $destination, Config $config);
}
