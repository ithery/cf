<?php

use League\Flysystem\Config;
use League\Flysystem\PathPrefixer;
use League\Flysystem\FileAttributes;
use League\Flysystem\ChecksumProvider;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToProvideChecksum;
use League\Flysystem\UnableToRetrieveMetadata;
use League\MimeTypeDetection\MimeTypeDetector;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

class CStorage_Adapter_DropboxAdapter implements ChecksumProvider, FilesystemAdapter {
    protected CVendor_Dropbox_Client $client;

    protected PathPrefixer $prefixer;

    protected MimeTypeDetector $mimeTypeDetector;

    public function __construct(
        CVendor_Dropbox_Client $client,
        $prefix = '',
        $mimeTypeDetector = null
    ) {
        $this->client = $client;
        $this->prefixer = new PathPrefixer($prefix);
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector();
    }

    public function getClient(): CVendor_Dropbox_Client {
        return $this->client;
    }

    public function fileExists($path) {
        $location = $this->applyPathPrefix($path);

        try {
            $meta = $this->client->getMetadata($location);

            return $meta['.tag'] === 'file';
        } catch (CVendor_Dropbox_Exception_BadRequestException $e) {
            return false;
        }
    }

    public function directoryExists($path) {
        $location = $this->applyPathPrefix($path);

        try {
            $meta = $this->client->getMetadata($location);

            return $meta['.tag'] === 'folder';
        } catch (CVendor_Dropbox_Exception_BadRequestException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function write($path, $contents, $config) {
        $location = $this->applyPathPrefix($path);

        try {
            $this->client->upload($location, $contents, 'overwrite');
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw UnableToWriteFile::atLocation($location, $exception->getMessage(), $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function writeStream($path, $contents, $config) {
        $location = $this->applyPathPrefix($path);

        try {
            $this->client->upload($location, $contents, 'overwrite');
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw UnableToWriteFile::atLocation($location, $exception->getMessage(), $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function read($path) {
        $object = $this->readStream($path);

        $contents = stream_get_contents($object);
        fclose($object);

        unset($object);

        return $contents;
    }

    /**
     * @inheritDoc
     */
    public function readStream($path) {
        $location = $this->applyPathPrefix($path);

        try {
            $stream = $this->client->download($location);
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw UnableToReadFile::fromLocation($location, $exception->getMessage(), $exception);
        }

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function delete($path) {
        $location = $this->applyPathPrefix($path);

        try {
            $this->client->delete($location);
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw UnableToDeleteFile::atLocation($location, $exception->getMessage(), $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteDirectory($path) {
        $location = $this->applyPathPrefix($path);

        try {
            $this->client->delete($location);
        } catch (UnableToDeleteFile $exception) {
            throw UnableToDeleteDirectory::atLocation($location, $exception->getPrevious()->getMessage(), $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function createDirectory($path, $config) {
        $location = $this->applyPathPrefix($path);

        try {
            $this->client->createFolder($location);
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw UnableToCreateDirectory::atLocation($location, $exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function setVisibility($path, $visibility) {
        throw UnableToSetVisibility::atLocation($path, 'Adapter does not support visibility controls.');
    }

    /**
     * @inheritDoc
     */
    public function visibility($path) {
        // Noop
        return new FileAttributes($path);
    }

    /**
     * @inheritDoc
     */
    public function mimeType($path) {
        return new FileAttributes(
            $path,
            null,
            null,
            null,
            $this->mimeTypeDetector->detectMimeTypeFromPath($path)
        );
    }

    /**
     * @inheritDoc
     */
    public function lastModified($path) {
        $location = $this->applyPathPrefix($path);

        try {
            $response = $this->client->getMetadata($location);
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw UnableToRetrieveMetadata::lastModified($location, $exception->getMessage());
        }

        $timestamp = (isset($response['server_modified'])) ? strtotime($response['server_modified']) : null;

        return new FileAttributes(
            $path,
            null,
            null,
            $timestamp
        );
    }

    /**
     * @inheritDoc
     */
    public function checksum($path, $config) {
        $algo = $config->get('checksum_algo', 'sha256');
        $location = $this->applyPathPrefix($path);

        try {
            $response = $this->client->getMetadata($location);
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw new UnableToProvideChecksum(
                'Unable to retrieve metadata.',
                $path,
                $exception,
            );
        }

        if (empty($response['content_hash'])) {
            throw new UnableToProvideChecksum(
                'Content-Hash not provided by Dropbox metadata.',
                $path,
            );
        }

        return $algo === 'sha256'
            ? $response['content_hash']
            : hash($algo, $response['content_hash']);
    }

    /**
     * @inheritDoc
     */
    public function fileSize($path) {
        $location = $this->applyPathPrefix($path);

        try {
            $response = $this->client->getMetadata($location);
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw UnableToRetrieveMetadata::lastModified($location, $exception->getMessage());
        }

        return new FileAttributes(
            $path,
            $response['size'] ?? null
        );
    }

    /**
     * @inheritDoc
     */
    public function listContents($path = '', $deep = false) {
        foreach ($this->iterateFolderContents($path, $deep) as $entry) {
            $storageAttrs = $this->normalizeResponse($entry);

            // Avoid including the base directory itself
            if ($storageAttrs->isDir() && $storageAttrs->path() === $path) {
                continue;
            }

            yield $storageAttrs;
        }
    }

    protected function iterateFolderContents(string $path = '', bool $deep = false): Generator {
        $location = $this->applyPathPrefix($path);

        try {
            $result = $this->client->listFolder($location, $deep);
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            return;
        }

        yield from $result['entries'];

        while ($result['has_more']) {
            $result = $this->client->listFolderContinue($result['cursor']);
            yield from $result['entries'];
        }
    }

    protected function normalizeResponse(array $response): StorageAttributes {
        $timestamp = (isset($response['server_modified'])) ? strtotime($response['server_modified']) : null;

        if ($response['.tag'] === 'folder') {
            $normalizedPath = ltrim($this->prefixer->stripDirectoryPrefix($response['path_display']), '/');

            return new DirectoryAttributes(
                $normalizedPath,
                null,
                $timestamp
            );
        }

        $normalizedPath = ltrim($this->prefixer->stripPrefix($response['path_display']), '/');

        return new FileAttributes(
            $normalizedPath,
            $response['size'] ?? null,
            null,
            $timestamp,
            $this->mimeTypeDetector->detectMimeTypeFromPath($normalizedPath)
        );
    }

    /**
     * @inheritDoc
     */
    public function move($source, $destination, $config) {
        $path = $this->applyPathPrefix($source);
        $newPath = $this->applyPathPrefix($destination);

        try {
            $this->client->move($path, $newPath);
        } catch (CVendor_Dropbox_Exception_BadRequestException $exception) {
            throw UnableToMoveFile::fromLocationTo($path, $newPath, $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function copy($source, $destination, $config) {
        $path = $this->applyPathPrefix($source);
        $newPath = $this->applyPathPrefix($destination);

        try {
            $this->client->copy($path, $newPath);
        } catch (CVendor_Dropbox_Exception_BadRequestException $e) {
            throw UnableToCopyFile::fromLocationTo($path, $newPath, $e);
        }
    }

    protected function applyPathPrefix($path): string {
        return '/' . trim($this->prefixer->prefixPath($path), '/');
    }

    public function getUrl(string $path): string {
        return $this->client->getTemporaryLink($path);
    }
}
