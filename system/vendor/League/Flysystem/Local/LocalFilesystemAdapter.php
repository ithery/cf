<?php

namespace League\Flysystem\Local;

use Generator;
use SplFileInfo;
use const LOCK_EX;
use function chmod;
use function mkdir;
use function is_dir;
use function rename;
use function dirname;
use function is_file;
use DirectoryIterator;
use FilesystemIterator;
use function file_exists;
use function clearstatcache;
use function error_get_last;
use League\Flysystem\Config;
use const DIRECTORY_SEPARATOR;
use function error_clear_last;
use RecursiveIteratorIterator;
use function file_put_contents;
use RecursiveDirectoryIterator;
use League\Flysystem\PathPrefixer;
use League\Flysystem\FileAttributes;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\SymbolicLinkEncountered;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToRetrieveMetadata;
use League\MimeTypeDetection\MimeTypeDetector;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

class LocalFilesystemAdapter implements FilesystemAdapter {
    /**
     * @var int
     */
    const SKIP_LINKS = 0001;

    /**
     * @var int
     */
    const DISALLOW_LINKS = 0002;

    /**
     * @var PathPrefixer
     */
    private $prefixer;

    /**
     * @var int
     */
    private $writeFlags;

    /**
     * @var int
     */
    private $linkHandling;

    /**
     * @var VisibilityConverter
     */
    private $visibility;

    /**
     * @var MimeTypeDetector
     */
    private $mimeTypeDetector;

    /**
     * @param string                   $location
     * @param null|VisibilityConverter $visibility
     * @param [type] $writeFlags
     * @param [type] $linkHandling
     * @param null|MimeTypeDetector $mimeTypeDetector
     */
    public function __construct(
        $location,
        VisibilityConverter $visibility = null,
        $writeFlags = LOCK_EX,
        $linkHandling = self::DISALLOW_LINKS,
        MimeTypeDetector $mimeTypeDetector = null
    ) {
        $this->prefixer = new PathPrefixer($location, DIRECTORY_SEPARATOR);
        $this->writeFlags = $writeFlags;
        $this->linkHandling = $linkHandling;
        $this->visibility = $visibility ?: new PortableVisibilityConverter();
        $this->ensureDirectoryExists($location, $this->visibility->defaultForDirectories());
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector();
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     *
     * @return void
     */
    public function write($path, $contents, Config $config) {
        $this->writeToFile($path, $contents, $config);
    }

    /**
     * @param string $path
     * @param [type] $contents
     * @param Config $config
     *
     * @return void
     */
    public function writeStream($path, $contents, Config $config) {
        $this->writeToFile($path, $contents, $config);
    }

    /**
     * @param string          $path
     * @param resource|string $contents
     * @param Config          $config
     *
     * @return void
     */
    private function writeToFile($path, $contents, Config $config) {
        $prefixedLocation = $this->prefixer->prefixPath($path);
        $this->ensureDirectoryExists(
            dirname($prefixedLocation),
            $this->resolveDirectoryVisibility($config->get(Config::OPTION_DIRECTORY_VISIBILITY))
        );
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }

        if (@file_put_contents($prefixedLocation, $contents, $this->writeFlags) === false) {
            throw UnableToWriteFile::atLocation($path, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }

        if ($visibility = $config->get(Config::OPTION_VISIBILITY)) {
            $this->setVisibility($path, (string) $visibility);
        }
    }

    public function delete($path) {
        $location = $this->prefixer->prefixPath($path);

        if (!file_exists($location)) {
            return;
        }
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }

        if (!@unlink($location)) {
            throw UnableToDeleteFile::atLocation($location, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }
    }

    public function deleteDirectory($prefix) {
        $location = $this->prefixer->prefixPath($prefix);

        if (!is_dir($location)) {
            return;
        }

        $contents = $this->listDirectoryRecursively($location, RecursiveIteratorIterator::CHILD_FIRST);

        /** @var SplFileInfo $file */
        foreach ($contents as $file) {
            if (!$this->deleteFileInfoObject($file)) {
                throw UnableToDeleteDirectory::atLocation($prefix, 'Unable to delete file at ' . $file->getPathname());
            }
        }

        unset($contents);

        if (!@rmdir($location)) {
            throw UnableToDeleteDirectory::atLocation($prefix, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }
    }

    /**
     * @param string $path
     * @param [type] $mode
     *
     * @return Generator
     */
    private function listDirectoryRecursively(
        string $path,
        int $mode = RecursiveIteratorIterator::SELF_FIRST
    ) {
        yield from (new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            $mode
        ));
    }

    /**
     * @param SplFileInfo $file
     *
     * @return bool
     */
    protected function deleteFileInfoObject(SplFileInfo $file) {
        switch ($file->getType()) {
            case 'dir':
                return @rmdir((string) $file->getRealPath());
            case 'link':
                return @unlink((string) $file->getPathname());
            default:
                return @unlink((string) $file->getRealPath());
        }
    }

    public function listContents($path, $deep) {
        $location = $this->prefixer->prefixPath($path);

        if (!is_dir($location)) {
            return;
        }

        /** @var SplFileInfo[] $iterator */
        $iterator = $deep ? $this->listDirectoryRecursively($location) : $this->listDirectory($location);

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isLink()) {
                if ($this->linkHandling & self::SKIP_LINKS) {
                    continue;
                }

                throw SymbolicLinkEncountered::atLocation($fileInfo->getPathname());
            }

            $path = $this->prefixer->stripPrefix($fileInfo->getPathname());
            $lastModified = $fileInfo->getMTime();
            $isDirectory = $fileInfo->isDir();
            $permissions = octdec(substr(sprintf('%o', $fileInfo->getPerms()), -4));
            $visibility = $isDirectory ? $this->visibility->inverseForDirectory($permissions) : $this->visibility->inverseForFile($permissions);

            yield $isDirectory ? new DirectoryAttributes(str_replace('\\', '/', $path), $visibility, $lastModified) : new FileAttributes(
                str_replace('\\', '/', $path),
                $fileInfo->getSize(),
                $visibility,
                $lastModified
            );
        }
    }

    public function move($source, $destination, Config $config) {
        $sourcePath = $this->prefixer->prefixPath($source);
        $destinationPath = $this->prefixer->prefixPath($destination);
        $this->ensureDirectoryExists(
            dirname($destinationPath),
            $this->resolveDirectoryVisibility($config->get(Config::OPTION_DIRECTORY_VISIBILITY))
        );

        if (!@rename($sourcePath, $destinationPath)) {
            throw UnableToMoveFile::fromLocationTo($sourcePath, $destinationPath);
        }
    }

    public function copy($source, $destination, Config $config) {
        $sourcePath = $this->prefixer->prefixPath($source);
        $destinationPath = $this->prefixer->prefixPath($destination);
        $this->ensureDirectoryExists(
            dirname($destinationPath),
            $this->resolveDirectoryVisibility($config->get(Config::OPTION_DIRECTORY_VISIBILITY))
        );

        if (!@copy($sourcePath, $destinationPath)) {
            throw UnableToCopyFile::fromLocationTo($sourcePath, $destinationPath);
        }
    }

    public function read($path) {
        $location = $this->prefixer->prefixPath($path);
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }
        $contents = @file_get_contents($location);

        if ($contents === false) {
            throw UnableToReadFile::fromLocation($path, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }

        return $contents;
    }

    public function readStream($path) {
        $location = $this->prefixer->prefixPath($path);
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }
        $contents = @fopen($location, 'rb');

        if ($contents === false) {
            throw UnableToReadFile::fromLocation($path, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }

        return $contents;
    }

    /**
     * @param string $dirname
     * @param int    $visibility
     *
     * @return void
     */
    protected function ensureDirectoryExists($dirname, $visibility) {
        if (is_dir($dirname)) {
            return;
        }
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }

        if (!@mkdir($dirname, $visibility, true)) {
            $mkdirError = error_get_last();
        }

        clearstatcache(true, $dirname);

        if (!is_dir($dirname)) {
            $errorMessage = isset($mkdirError['message']) ? $mkdirError['message'] : '';

            throw UnableToCreateDirectory::atLocation($dirname, $errorMessage);
        }
    }

    /**
     * @param string $location
     *
     * @return bool
     */
    public function fileExists($location) {
        $location = $this->prefixer->prefixPath($location);

        return is_file($location);
    }

    /**
     * @param string $location
     *
     * @return bool
     */
    public function directoryExists($location) {
        $location = $this->prefixer->prefixPath($location);

        return is_dir($location);
    }

    /**
     * @param string $path
     * @param Config $config
     *
     * @return void
     */
    public function createDirectory($path, Config $config) {
        $location = $this->prefixer->prefixPath($path);
        $visibility = $config->get(Config::OPTION_VISIBILITY, $config->get(Config::OPTION_DIRECTORY_VISIBILITY));
        $permissions = $this->resolveDirectoryVisibility($visibility);

        if (is_dir($location)) {
            $this->setPermissions($location, $permissions);

            return;
        }
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }

        if (!@mkdir($location, $permissions, true)) {
            throw UnableToCreateDirectory::atLocation($path, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }
    }

    /**
     * @param string $path
     * @param string $visibility
     *
     * @return void
     */
    public function setVisibility($path, $visibility) {
        $path = $this->prefixer->prefixPath($path);
        $visibility = is_dir($path) ? $this->visibility->forDirectory($visibility) : $this->visibility->forFile(
            $visibility
        );

        $this->setPermissions($path, $visibility);
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function visibility($path) {
        $location = $this->prefixer->prefixPath($path);
        clearstatcache(false, $location);
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }
        $fileperms = @fileperms($location);

        if ($fileperms === false) {
            throw UnableToRetrieveMetadata::visibility($path, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }

        $permissions = $fileperms & 0777;
        $visibility = $this->visibility->inverseForFile($permissions);

        return new FileAttributes($path, null, $visibility);
    }

    /**
     * @param null|string $visibility
     *
     * @return int
     */
    private function resolveDirectoryVisibility($visibility = null) {
        return $visibility === null ? $this->visibility->defaultForDirectories() : $this->visibility->forDirectory(
            $visibility
        );
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function mimeType($path) {
        $location = $this->prefixer->prefixPath($path);
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }
        $mimeType = $this->mimeTypeDetector->detectMimeTypeFromFile($location);

        if ($mimeType === null) {
            throw UnableToRetrieveMetadata::mimeType($path, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }

        return new FileAttributes($path, null, null, null, $mimeType);
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function lastModified($path) {
        $location = $this->prefixer->prefixPath($path);
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }
        $lastModified = @filemtime($location);

        if ($lastModified === false) {
            throw UnableToRetrieveMetadata::lastModified($path, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
        }

        return new FileAttributes($path, null, null, $lastModified);
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function fileSize($path) {
        $location = $this->prefixer->prefixPath($path);
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }

        if (is_file($location) && ($fileSize = @filesize($location)) !== false) {
            return new FileAttributes($path, $fileSize);
        }

        throw UnableToRetrieveMetadata::fileSize($path, isset(error_get_last()['message']) ? error_get_last()['message'] : '');
    }

    /**
     * @param string $location
     *
     * @return Generator
     */
    private function listDirectory($location) {
        $iterator = new DirectoryIterator($location);

        foreach ($iterator as $item) {
            if ($item->isDot()) {
                continue;
            }

            yield $item;
        }
    }

    /**
     * @param string $location
     * @param int    $visibility
     *
     * @return void
     */
    private function setPermissions($location, $visibility) {
        if (\function_exists('error_clear_last')) {
            error_clear_last();
        }
        if (!@chmod($location, $visibility)) {
            $extraMessage = isset(error_get_last()['message']) ? error_get_last()['message'] : '';

            throw UnableToSetVisibility::atLocation($this->prefixer->stripPrefix($location), $extraMessage);
        }
    }
}
