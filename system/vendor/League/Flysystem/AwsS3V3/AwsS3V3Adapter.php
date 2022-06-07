<?php

declare(strict_types=1);

namespace League\Flysystem\AwsS3V3;

use Generator;
use Throwable;
use function trim;
use Aws\Api\DateTimeResult;
use League\Flysystem\Config;
use Aws\S3\S3ClientInterface;
use League\Flysystem\Visibility;
use League\Flysystem\PathPrefixer;
use League\Flysystem\FileAttributes;
use Psr\Http\Message\StreamInterface;
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
use League\Flysystem\UnableToRetrieveMetadata;
use League\MimeTypeDetection\MimeTypeDetector;
use League\Flysystem\FilesystemOperationFailed;
use League\Flysystem\UnableToCheckFileExistence;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

use League\Flysystem\UnableToCheckDirectoryExistence;

class AwsS3V3Adapter implements FilesystemAdapter {
    /**
     * @var string[]
     */
    public const AVAILABLE_OPTIONS = [
        'ACL',
        'CacheControl',
        'ContentDisposition',
        'ContentEncoding',
        'ContentLength',
        'ContentType',
        'Expires',
        'GrantFullControl',
        'GrantRead',
        'GrantReadACP',
        'GrantWriteACP',
        'Metadata',
        'MetadataDirective',
        'RequestPayer',
        'SSECustomerAlgorithm',
        'SSECustomerKey',
        'SSECustomerKeyMD5',
        'SSEKMSKeyId',
        'ServerSideEncryption',
        'StorageClass',
        'Tagging',
        'WebsiteRedirectLocation',
    ];

    /**
     * @var string[]
     */
    public const MUP_AVAILABLE_OPTIONS = [
        'before_upload',
        'concurrency',
        'mup_threshold',
        'params',
        'part_size',
    ];

    /**
     * @var string[]
     */
    private const EXTRA_METADATA_FIELDS = [
        'Metadata',
        'StorageClass',
        'ETag',
        'VersionId',
    ];

    /**
     * @var S3ClientInterface
     */
    private $client;

    /**
     * @var PathPrefixer
     */
    private $prefixer;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var VisibilityConverter
     */
    private $visibility;

    /**
     * @var MimeTypeDetector
     */
    private $mimeTypeDetector;

    /**
     * @var array
     */
    private $options;

    /**
     * @var bool
     */
    private $streamReads;

    public function __construct(
        S3ClientInterface $client,
        string $bucket,
        string $prefix = '',
        VisibilityConverter $visibility = null,
        MimeTypeDetector $mimeTypeDetector = null,
        array $options = [],
        bool $streamReads = true
    ) {
        $this->client = $client;
        $this->prefixer = new PathPrefixer($prefix);
        $this->bucket = $bucket;
        $this->visibility = $visibility ?: new PortableVisibilityConverter();
        $this->mimeTypeDetector = $mimeTypeDetector ?: new FinfoMimeTypeDetector();
        $this->options = $options;
        $this->streamReads = $streamReads;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path) {
        try {
            return $this->client->doesObjectExist($this->bucket, $this->prefixer->prefixPath($path), $this->options);
        } catch (Throwable $exception) {
            throw UnableToCheckFileExistence::forLocation($path, $exception);
        }
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function directoryExists($path) {
        try {
            $prefix = $this->prefixer->prefixDirectoryPath($path);
            $options = ['Bucket' => $this->bucket, 'Prefix' => $prefix, 'MaxKeys' => 1, 'Delimiter' => '/'];
            $command = $this->client->getCommand('ListObjects', $options);
            $result = $this->client->execute($command);

            return $result->hasKey('Contents') || $result->hasKey('CommonPrefixes');
        } catch (Throwable $exception) {
            throw UnableToCheckDirectoryExistence::forLocation($path, $exception);
        }
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     *
     * @return void
     */
    public function write($path, $contents, Config $config) {
        $this->upload($path, $contents, $config);
    }

    /**
     * @param string          $path
     * @param string|resource $body
     * @param Config          $config
     *
     * @return void
     */
    private function upload($path, $body, Config $config) {
        $key = $this->prefixer->prefixPath($path);
        $options = $this->createOptionsFromConfig($config);
        $acl = $options['params']['ACL'] ?? $this->determineAcl($config);
        $shouldDetermineMimetype = $body !== '' && !array_key_exists('ContentType', $options['params']);

        if ($shouldDetermineMimetype && $mimeType = $this->mimeTypeDetector->detectMimeType($key, $body)) {
            $options['params']['ContentType'] = $mimeType;
        }

        try {
            $this->client->upload($this->bucket, $key, $body, $acl, $options);
        } catch (Throwable $exception) {
            throw UnableToWriteFile::atLocation($path, '', $exception);
        }
    }

    /**
     * @param Config $config
     *
     * @return string
     */
    private function determineAcl(Config $config) {
        $visibility = (string) $config->get(Config::OPTION_VISIBILITY, Visibility::VISIBILITY_PRIVATE);

        return $this->visibility->visibilityToAcl($visibility);
    }

    /**
     * @param Config $config
     *
     * @return array
     */
    private function createOptionsFromConfig(Config $config) {
        $config = $config->withDefaults($this->options);
        $options = ['params' => []];

        if ($mimetype = $config->get('mimetype')) {
            $options['params']['ContentType'] = $mimetype;
        }

        foreach (static::AVAILABLE_OPTIONS as $option) {
            $value = $config->get($option, '__NOT_SET__');

            if ($value !== '__NOT_SET__') {
                $options['params'][$option] = $value;
            }
        }

        foreach (static::MUP_AVAILABLE_OPTIONS as $option) {
            $value = $config->get($option, '__NOT_SET__');

            if ($value !== '__NOT_SET__') {
                $options[$option] = $value;
            }
        }

        return $options;
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     *
     * @return void
     */
    public function writeStream($path, $contents, Config $config) {
        $this->upload($path, $contents, $config);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function read($path) {
        $body = $this->readObject($path, false);

        return (string) $body->getContents();
    }

    /**
     * @param string $path
     *
     * @return resource
     */
    public function readStream($path) {
        /** @var resource $resource */
        $resource = $this->readObject($path, true)->detach();

        return $resource;
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function delete($path) {
        $arguments = ['Bucket' => $this->bucket, 'Key' => $this->prefixer->prefixPath($path)];
        $command = $this->client->getCommand('DeleteObject', $arguments);

        try {
            $this->client->execute($command);
        } catch (Throwable $exception) {
            throw UnableToDeleteFile::atLocation($path, '', $exception);
        }
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function deleteDirectory($path) {
        $prefix = $this->prefixer->prefixPath($path);
        $prefix = ltrim(rtrim($prefix, '/') . '/', '/');

        try {
            $this->client->deleteMatchingObjects($this->bucket, $prefix);
        } catch (Throwable $exception) {
            throw UnableToCreateDirectory::dueToFailure($path, $exception);
        }
    }

    /**
     * @param string $path
     * @param Config $config
     *
     * @return void
     */
    public function createDirectory($path, Config $config) {
        $defaultVisibility = $config->get('directory_visibility', $this->visibility->defaultForDirectories());
        $config = $config->withDefaults(['visibility' => $defaultVisibility]);
        $this->upload(rtrim($path, '/') . '/', '', $config);
    }

    /**
     * @param string $path
     * @param string $visibility
     *
     * @return void
     */
    public function setVisibility($path, $visibility) {
        $arguments = [
            'Bucket' => $this->bucket,
            'Key' => $this->prefixer->prefixPath($path),
            'ACL' => $this->visibility->visibilityToAcl($visibility),
        ];
        $command = $this->client->getCommand('PutObjectAcl', $arguments);

        try {
            $this->client->execute($command);
        } catch (Throwable $exception) {
            throw UnableToSetVisibility::atLocation($path, '', $exception);
        }
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function visibility($path) {
        $arguments = ['Bucket' => $this->bucket, 'Key' => $this->prefixer->prefixPath($path)];
        $command = $this->client->getCommand('GetObjectAcl', $arguments);

        try {
            $result = $this->client->execute($command);
        } catch (Throwable $exception) {
            throw UnableToRetrieveMetadata::visibility($path, '', $exception);
        }

        $visibility = $this->visibility->aclToVisibility((array) $result->get('Grants'));

        return new FileAttributes($path, null, $visibility);
    }

    /**
     * @param string $path
     * @param string $type
     *
     * @return FileAttributes
     */
    private function fetchFileMetadata($path, $type) {
        $arguments = ['Bucket' => $this->bucket, 'Key' => $this->prefixer->prefixPath($path)];
        $command = $this->client->getCommand('HeadObject', $arguments);

        try {
            $result = $this->client->execute($command);
        } catch (Throwable $exception) {
            throw UnableToRetrieveMetadata::create($path, $type, '', $exception);
        }

        $attributes = $this->mapS3ObjectMetadata($result->toArray(), $path);

        if (!$attributes instanceof FileAttributes) {
            throw UnableToRetrieveMetadata::create($path, $type, '');
        }

        return $attributes;
    }

    /**
     * @param array|TYield $metadata
     * @param string       $path
     *
     * @return StorageAttributes
     */
    private function mapS3ObjectMetadata($metadata, $path) {
        if (substr($path, -1) === '/') {
            return new DirectoryAttributes(rtrim($path, '/'));
        }

        $mimetype = $metadata['ContentType'] ?? null;
        $fileSize = $metadata['ContentLength'] ?? $metadata['Size'] ?? null;
        $fileSize = $fileSize === null ? null : (int) $fileSize;
        $dateTime = $metadata['LastModified'] ?? null;
        $lastModified = $dateTime instanceof DateTimeResult ? $dateTime->getTimeStamp() : null;

        return new FileAttributes(
            $path,
            $fileSize,
            null,
            $lastModified,
            $mimetype,
            $this->extractExtraMetadata($metadata)
        );
    }

    /**
     * @param array $metadata
     *
     * @return array
     */
    private function extractExtraMetadata(array $metadata) {
        $extracted = [];

        foreach (static::EXTRA_METADATA_FIELDS as $field) {
            if (isset($metadata[$field]) && $metadata[$field] !== '') {
                $extracted[$field] = $metadata[$field];
            }
        }

        return $extracted;
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function mimeType($path) {
        $attributes = $this->fetchFileMetadata($path, FileAttributes::ATTRIBUTE_MIME_TYPE);

        if ($attributes->mimeType() === null) {
            throw UnableToRetrieveMetadata::mimeType($path);
        }

        return $attributes;
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function lastModified($path) {
        $attributes = $this->fetchFileMetadata($path, FileAttributes::ATTRIBUTE_LAST_MODIFIED);

        if ($attributes->lastModified() === null) {
            throw UnableToRetrieveMetadata::lastModified($path);
        }

        return $attributes;
    }

    /**
     * @param string $path
     *
     * @return FileAttributes
     */
    public function fileSize($path) {
        $attributes = $this->fetchFileMetadata($path, FileAttributes::ATTRIBUTE_FILE_SIZE);

        if ($attributes->fileSize() === null) {
            throw UnableToRetrieveMetadata::fileSize($path);
        }

        return $attributes;
    }

    /**
     * @param string $path
     * @param bool   $deep
     *
     * @return iterable
     */
    public function listContents($path, $deep) {
        $prefix = trim($this->prefixer->prefixPath($path), '/');
        $prefix = empty($prefix) ? '' : $prefix . '/';
        $options = ['Bucket' => $this->bucket, 'Prefix' => $prefix];

        if ($deep === false) {
            $options['Delimiter'] = '/';
        }

        $listing = $this->retrievePaginatedListing($options);

        foreach ($listing as $item) {
            $key = isset($item['Key']) ? $item['Key'] : $item['Prefix'];

            if ($key === $prefix) {
                continue;
            }

            yield $this->mapS3ObjectMetadata($item, $this->prefixer->stripPrefix($key));
        }
    }

    /**
     * @param array $options
     *
     * @return Generator
     */
    private function retrievePaginatedListing(array $options) {
        $resultPaginator = $this->client->getPaginator('ListObjects', $options + $this->options);

        foreach ($resultPaginator as $result) {
            yield from ($result->get('CommonPrefixes') ?: []);
            yield from ($result->get('Contents') ?: []);
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
        try {
            $this->copy($source, $destination, $config);
            $this->delete($source);
        } catch (FilesystemOperationFailed $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }

    /**
     * @param string $source
     * @param string $destination
     * @param Config $config
     *
     * @return void
     */
    public function copy($source, $destination, Config $config) {
        try {
            /** @var string $visibility */
            $visibility = $this->visibility($source)->visibility();
        } catch (Throwable $exception) {
            throw UnableToCopyFile::fromLocationTo(
                $source,
                $destination,
                $exception
            );
        }

        try {
            $this->client->copy(
                $this->bucket,
                $this->prefixer->prefixPath($source),
                $this->bucket,
                $this->prefixer->prefixPath($destination),
                $this->visibility->visibilityToAcl($visibility),
                $this->createOptionsFromConfig($config)['params']
            );
        } catch (Throwable $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    /**
     * @param string $path
     * @param bool   $wantsStream
     *
     * @return StreamInterface
     */
    private function readObject($path, $wantsStream) {
        $options = ['Bucket' => $this->bucket, 'Key' => $this->prefixer->prefixPath($path)];

        if ($wantsStream && $this->streamReads && !isset($this->options['@http']['stream'])) {
            $options['@http']['stream'] = true;
        }

        $command = $this->client->getCommand('GetObject', $options + $this->options);

        try {
            return $this->client->execute($command)->get('Body');
        } catch (Throwable $exception) {
            throw UnableToReadFile::fromLocation($path, '', $exception);
        }
    }
}
