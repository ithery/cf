<?php

namespace League\Flysystem;

interface FilesystemWriter {
    /**
     * @param string $location
     * @param string $contents
     *
     * @throws UnableToWriteFile
     * @throws FilesystemException
     *
     * @return void
     */
    public function write($location, $contents, array $config = []);

    /**
     * @param string $location
     * @param mixed  $contents
     *
     * @throws UnableToWriteFile
     * @throws FilesystemException
     *
     * @return void
     */
    public function writeStream($location, $contents, array $config = []);

    /**
     * @param string $path
     * @param string $visibility
     *
     * @throws UnableToSetVisibility
     * @throws FilesystemException
     *
     * @return void
     */
    public function setVisibility($path, $visibility);

    /**
     * @param string $location
     *
     * @throws UnableToDeleteFile
     * @throws FilesystemException
     *
     * @return void
     */
    public function delete($location);

    /**
     * @param string $location
     *
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     *
     * @return void
     */
    public function deleteDirectory($location);

    /**
     * @param string $location
     *
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     *
     * @return void
     */
    public function createDirectory($location, array $config = []);

    /**
     * @param string $source
     * @param string $destination
     *
     * @throws UnableToMoveFile
     * @throws FilesystemException
     *
     * @return void
     */
    public function move($source, $destination, array $config = []);

    /**
     * @param string $source
     * @param string $destination
     *
     * @throws UnableToCopyFile
     * @throws FilesystemException
     *
     * @return void
     */
    public function copy($source, $destination, array $config = []);
}
