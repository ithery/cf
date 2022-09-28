<?php

declare(strict_types=1);

namespace League\Flysystem\InMemory;

use function rtrim;
use function strpos;
use function array_keys;
use League\Flysystem\Config;
use League\Flysystem\Visibility;
use League\Flysystem\FileAttributes;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\UnableToSetVisibility;

use League\Flysystem\UnableToRetrieveMetadata;
use League\MimeTypeDetection\MimeTypeDetector;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

class InMemoryFilesystemAdapter implements FilesystemAdapter {
    const DUMMY_FILE_FOR_FORCED_LISTING_IN_FLYSYSTEM_TEST = '______DUMMY_FILE_FOR_FORCED_LISTING_IN_FLYSYSTEM_TEST';

    /**
     * @var InMemoryFile[]
     */
    private $files = [];

    /**
     * @var string
     */
    private $defaultVisibility;

    /**
     * @var MimeTypeDetector
     */
    private $mimeTypeDetector;

    public function __construct(string $defaultVisibility = Visibility::VISIBILITY_PUBLIC, MimeTypeDetector $mimeTypeDetector = null) {
        $this->defaultVisibility = $defaultVisibility;
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector();
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path) {
        return array_key_exists($this->preparePath($path), $this->files);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     *
     * @return void
     */
    public function write($path, $contents, Config $config) {
        $path = $this->preparePath($path);
        $file = $this->files[$path] = $this->files[$path] ?? new InMemoryFile();
        $file->updateContents($contents, $config->get('timestamp'));

        $visibility = $config->get(Config::OPTION_VISIBILITY, $this->defaultVisibility);
        $file->setVisibility($visibility);
    }

    /**
     * @param string          $path
     * @param string|resource $contents
     * @param Config          $config
     *
     * @return void
     */
    public function writeStream($path, $contents, Config $config) {
        $this->write($path, (string) stream_get_contents($contents), $config);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function read($path) {
        $path = $this->preparePath($path);

        if (array_key_exists($path, $this->files) === false) {
            throw UnableToReadFile::fromLocation($path, 'file does not exist');
        }

        return $this->files[$path]->read();
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function readStream($path) {
        $path = $this->preparePath($path);

        if (array_key_exists($path, $this->files) === false) {
            throw UnableToReadFile::fromLocation($path, 'file does not exist');
        }

        return $this->files[$path]->readStream();
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function delete($path) {
        unset($this->files[$this->preparePath($path)]);
    }

    /**
     * @param string $prefix
     *
     * @return void
     */
    public function deleteDirectory($prefix) {
        $prefix = $this->preparePath($prefix);
        $prefix = rtrim($prefix, '/') . '/';

        foreach (array_keys($this->files) as $path) {
            if (strpos($path, $prefix) === 0) {
                unset($this->files[$path]);
            }
        }
    }

    /**
     * @param string $path
     * @param Config $config
     *
     * @return void
     */
    public function createDirectory($path, Config $config) {
        $filePath = rtrim($path, '/') . '/' . self::DUMMY_FILE_FOR_FORCED_LISTING_IN_FLYSYSTEM_TEST;
        $this->write($filePath, '', $config);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function directoryExists($path) {
        $prefix = $this->preparePath($path);
        $prefix = rtrim($prefix, '/') . '/';

        foreach (array_keys($this->files) as $path) {
            if (strpos($path, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $path
     * @param string $visibility
     *
     * @return void
     */
    public function setVisibility($path, $visibility) {
        $path = $this->preparePath($path);

        if (array_key_exists($path, $this->files) === false) {
            throw UnableToSetVisibility::atLocation($path, 'file does not exist');
        }

        $this->files[$path]->setVisibility($visibility);
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function visibility($path) {
        $path = $this->preparePath($path);

        if (array_key_exists($path, $this->files) === false) {
            throw UnableToRetrieveMetadata::visibility($path, 'file does not exist');
        }

        return new FileAttributes($path, null, $this->files[$path]->visibility());
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function mimeType($path) {
        $preparedPath = $this->preparePath($path);

        if (array_key_exists($preparedPath, $this->files) === false) {
            throw UnableToRetrieveMetadata::mimeType($path, 'file does not exist');
        }

        $mimeType = $this->mimeTypeDetector->detectMimeType($path, $this->files[$preparedPath]->read());

        if ($mimeType === null) {
            throw UnableToRetrieveMetadata::mimeType($path);
        }

        return new FileAttributes($preparedPath, null, null, null, $mimeType);
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function lastModified($path) {
        $path = $this->preparePath($path);

        if (array_key_exists($path, $this->files) === false) {
            throw UnableToRetrieveMetadata::lastModified($path, 'file does not exist');
        }

        return new FileAttributes($path, null, null, $this->files[$path]->lastModified());
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function fileSize($path) {
        $path = $this->preparePath($path);

        if (array_key_exists($path, $this->files) === false) {
            throw UnableToRetrieveMetadata::fileSize($path, 'file does not exist');
        }

        return new FileAttributes($path, $this->files[$path]->fileSize());
    }

    /**
     * @param string $path
     * @param bool   $deep
     *
     * @return iterable
     */
    public function listContents($path, $deep) {
        $prefix = rtrim($this->preparePath($path), '/') . '/';
        $prefixLength = strlen($prefix);
        $listedDirectories = [];

        foreach ($this->files as $path => $file) {
            if (substr($path, 0, $prefixLength) === $prefix) {
                $subPath = substr($path, $prefixLength);
                $dirname = dirname($subPath);

                if ($dirname !== '.') {
                    $parts = explode('/', $dirname);
                    $dirPath = '';

                    foreach ($parts as $index => $part) {
                        if ($deep === false && $index >= 1) {
                            break;
                        }

                        $dirPath .= $part . '/';

                        if (!in_array($dirPath, $listedDirectories)) {
                            $listedDirectories[] = $dirPath;
                            yield new DirectoryAttributes(trim($prefix . $dirPath, '/'));
                        }
                    }
                }

                $dummyFilename = self::DUMMY_FILE_FOR_FORCED_LISTING_IN_FLYSYSTEM_TEST;
                if (substr($path, -strlen($dummyFilename)) === $dummyFilename) {
                    continue;
                }

                if ($deep === true || strpos($subPath, '/') === false) {
                    yield new FileAttributes(ltrim($path, '/'), $file->fileSize(), $file->visibility(), $file->lastModified(), $file->mimeType());
                }
            }
        }
    }

    /**
     * @param string $source
     * @param string $destination
     * @param Config $config
     *
     * @return void
     */
    public function move($source, $destination, Config $config) {
        $source = $this->preparePath($source);
        $destination = $this->preparePath($destination);

        if (!$this->fileExists($source) || $this->fileExists($destination)) {
            throw UnableToMoveFile::fromLocationTo($source, $destination);
        }

        $this->files[$destination] = $this->files[$source];
        unset($this->files[$source]);
    }

    /**
     * @param string $source
     * @param string $destination
     * @param Config $config
     *
     * @return void
     */
    public function copy($source, $destination, Config $config) {
        $source = $this->preparePath($source);
        $destination = $this->preparePath($destination);

        if (!$this->fileExists($source)) {
            throw UnableToCopyFile::fromLocationTo($source, $destination);
        }

        $lastModified = $config->get('timestamp', time());

        $this->files[$destination] = $this->files[$source]->withLastModified($lastModified);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function preparePath($path) {
        return '/' . ltrim($path, '/');
    }

    /**
     * @return void
     */
    public function deleteEverything() {
        $this->files = [];
    }
}
