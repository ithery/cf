<?php

use League\Flysystem\Config;
use League\Flysystem\Visibility;
use League\Flysystem\PathPrefixer;
use League\Flysystem\FileAttributes;
use League\Flysystem\DirectoryListing;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\InvalidVisibilityProvided;

class CStorage_Adapter_BunnyCDNAdapter implements FilesystemAdapter {
    /**
     * Pull Zone URL.
     *
     * @var string
     */
    private $pullzone_url;

    /**
     * @var CStorage_Vendor_BunnyCDN_Client
     */
    private $client;

    /**
     * @param CStorage_Vendor_BunnyCDN_Client $client
     * @param string                          $pullzone_url
     */
    public function __construct(CStorage_Vendor_BunnyCDN_Client $client, $pullzone_url = '') {
        $this->client = $client;
        $this->pullzone_url = $pullzone_url;
    }

    /**
     * @param $source
     * @param $destination
     * @param Config $config
     *
     * @return void
     */
    public function copy($source, $destination, Config $config): void {
        try {
            $this->write($destination, $this->read($source), new Config());
            // @codeCoverageIgnoreStart
        } catch (UnableToReadFile|UnableToWriteFile $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $path
     * @param $contents
     * @param Config $config
     */
    public function write($path, $contents, Config $config): void {
        try {
            $this->client->upload($path, $contents);
            // @codeCoverageIgnoreStart
        } catch (CStorage_Vendor_BunnyCDN_Exception $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function read($path) {
        try {
            return $this->client->download($path);
            // @codeCoverageIgnoreStart
        } catch (CStorage_Vendor_BunnyCDN_Exception $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     * @param bool   $deep
     *
     * @return iterable
     */
    public function listContents($path = '', $deep = false) {
        try {
            $entries = $this->client->list($path);
            // @codeCoverageIgnoreStart
        } catch (CStorage_Vendor_BunnyCDN_Exception $e) {
            throw UnableToRetrieveMetadata::create($path, 'folder', $e->getMessage());
        }
        // @codeCoverageIgnoreEnd

        foreach ($entries as $item) {
            yield $this->normalizeObject($item);
        }

        // return new DirectoryListing($contents, $deep);

        // return array_map(function ($item) {
        //     return $this->normalizeObject($item);
        // }, $entries);

        // return $entries;
    }

    /**
     * @param array $bunny_file_array
     *
     * @return StorageAttributes
     */
    protected function normalizeObject(array $bunny_file_array): StorageAttributes {
        if ($bunny_file_array['IsDirectory']) {
            return new DirectoryAttributes(
                CStorage_Vendor_BunnyCDN_Util::normalizePath(
                    str_replace(
                        $bunny_file_array['StorageZoneName'] . '/',
                        '/',
                        $bunny_file_array['Path'] . $bunny_file_array['ObjectName']
                    )
                )
            );
        }

        return new FileAttributes(
            CStorage_Vendor_BunnyCDN_Util::normalizePath(
                str_replace(
                    $bunny_file_array['StorageZoneName'] . '/',
                    '/',
                    $bunny_file_array['Path'] . $bunny_file_array['ObjectName']
                )
            ),
            $bunny_file_array['Length'],
            Visibility::VISIBILITY_PUBLIC,
            date_create_from_format('Y-m-d\TH:i:s.v', $bunny_file_array['LastChanged'])->getTimestamp(),
            $bunny_file_array['ContentType'],
            $this->extractExtraMetadata($bunny_file_array)
        );
    }

    /**
     * @param array $bunny_file_array
     *
     * @return array
     */
    private function extractExtraMetadata(array $bunny_file_array): array {
        return [
            'type' => $bunny_file_array['IsDirectory'] ? 'dir' : 'file',
            'dirname' => CStorage_Vendor_BunnyCDN_Util::splitPathIntoDirectoryAndFile($bunny_file_array['Path'])['dir'],
            'guid' => $bunny_file_array['Guid'],
            'object_name' => $bunny_file_array['ObjectName'],
            'timestamp' => date_create_from_format('Y-m-d\TH:i:s.v', $bunny_file_array['LastChanged'])->getTimestamp(),
            'server_id' => $bunny_file_array['ServerId'],
            'user_id' => $bunny_file_array['UserId'],
            'date_created' => $bunny_file_array['DateCreated'],
            'storage_zone_name' => $bunny_file_array['StorageZoneName'],
            'storage_zone_id' => $bunny_file_array['StorageZoneId'],
            'checksum' => $bunny_file_array['Checksum'],
            'replicated_zones' => $bunny_file_array['ReplicatedZones'],
        ];
    }

    /**
     * @param $path
     * @param $contents
     * @param Config $config
     *
     * @return void
     */
    public function writeStream($path, $contents, Config $config): void {
        $this->write($path, stream_get_contents($contents), $config);
    }

    /**
     * @param $path
     *
     * @throws Exceptions\BunnyCDNException
     * @throws Exceptions\NotFoundException
     *
     * @return resource
     */
    public function readStream($path) {
        return $this->client->stream($path);
    }

    /**
     * @param string $path
     *
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     */
    public function deleteDirectory($path) {
        try {
            $this->client->delete(
                rtrim($path, '/') . '/'
            );
            // @codeCoverageIgnoreStart
        } catch (CStorage_Vendor_BunnyCDN_Exception $e) {
            throw UnableToDeleteDirectory::atLocation($path, $e->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     *
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     */
    public function createDirectory($path, Config $config) {
        try {
            $this->client->makeDirectory($path);
            // @codeCoverageIgnoreStart
        } catch (CStorage_Vendor_BunnyCDN_Exception $e) {
            // Lol apparently this is "idempotent" but there's an exception... Sure whatever..
            if ($e->getMessage() != 'Directory already exists') {
                throw UnableToCreateDirectory::atLocation($path, $e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     * @param string $visibility
     *
     * @throws InvalidVisibilityProvided
     * @throws FilesystemException
     */
    public function setVisibility($path, $visibility) {
        throw UnableToSetVisibility::atLocation($path, 'BunnyCDN does not support visibility');
    }

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     *
     * @return FileAttributes
     */
    public function visibility($path) {
        try {
            return new FileAttributes($this->getObject($path)->path(), null, $this->pullzone_url ? 'public' : 'private');
        } catch (UnableToReadFile $e) {
            throw new UnableToRetrieveMetadata($e->getMessage());
        }
    }

    /**
     * @param string $path
     *
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     *
     * @return FileAttributes
     * @codeCoverageIgnore
     */
    public function mimeType($path) {
        try {
            $object = $this->getObject($path);

            if ($object instanceof DirectoryAttributes) {
                throw new UnableToRetrieveMetadata('Cannot retrieve mimetype of folder');
            }

            /** @var FileAttributes $object */
            if (!$object->mimeType()) {
                throw new UnableToRetrieveMetadata('Unknown Mimetype');
            }

            return $object;
        } catch (UnableToReadFile $e) {
            throw new UnableToRetrieveMetadata($e->getMessage());
        }
    }

    /**
     * @param $path
     *
     * @return StorageAttributes|mixed
     */
    protected function getObject($path) {
        $list = (new DirectoryListing($this->listContents()))
            ->filter(function (StorageAttributes $item) use ($path) {
                return $item->path() === $path;
            })->toArray();

        if (count($list) === 1) {
            return $list[0];
        } elseif (count($list) > 1) {
            throw UnableToReadFile::fromLocation($path, 'More than one file was returned for path:"' . $path . '", contact package author.');
        } else {
            throw UnableToReadFile::fromLocation($path, 'Error 404:"' . $path . '"');
        }
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function lastModified($path) {
        try {
            return $this->getObject($path);
        } catch (UnableToReadFile $e) {
            throw new UnableToRetrieveMetadata($e->getMessage());
        }
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function fileSize($path) {
        try {
            $object = $this->getObject($path);

            if ($object instanceof DirectoryAttributes) {
                throw new UnableToRetrieveMetadata('Cannot retrieve size of folder');
            }

            return $object;
        } catch (UnableToReadFile $e) {
            throw new UnableToRetrieveMetadata($e->getMessage());
        }
    }

    /**
     * @param string $source
     * @param string $destination
     *
     * @throws UnableToMoveFile
     * @throws FilesystemException
     */
    public function move($source, $destination, Config $config) {
        try {
            $this->write($destination, $this->read($source), new Config());
            $this->delete($source);
        } catch (UnableToReadFile $e) {
            throw new UnableToMoveFile($e->getMessage());
        }
    }

    /**
     * @param $path
     *
     * @return void
     */
    public function delete($path) {
        try {
            $this->client->delete($path);
            // @codeCoverageIgnoreStart
        } catch (CStorage_Vendor_BunnyCDN_Exception $e) {
            if (!cstr::contains($e->getMessage(), '404')) { // Urgh
                throw UnableToDeleteFile::atLocation($path, $e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     *
     * @throws UnableToCheckExistence
     *
     * @return bool
     */
    public function directoryExists($path) {
        return $this->fileExists($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path) {
        $list = new DirectoryListing($this->listContents(
            CStorage_Vendor_BunnyCDN_Util::splitPathIntoDirectoryAndFile($path)['dir']
        ));

        $count = $list->filter(function (StorageAttributes $item) use ($path) {
            return CStorage_Vendor_BunnyCDN_Util::normalizePath($item->path()) === CStorage_Vendor_BunnyCDN_Util::normalizePath($path);
        })->toArray();

        return (bool) count($count);
    }

    /**
     * Method getUrl for users who want to use BunnyCDN's PullZone to retrieve a public URL.
     *
     * @param string $path
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getUrl($path) {
        if ($this->pullzone_url === '') {
            throw new RuntimeException('In order to get a visible URL for a BunnyCDN object, you must pass the "pullzone_url" parameter to the BunnyCDNAdapter.');
        }

        return rtrim($this->pullzone_url, '/') . '/' . ltrim($path, '/');
    }
}
