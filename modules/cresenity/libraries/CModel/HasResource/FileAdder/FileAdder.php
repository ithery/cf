<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 1, 2019, 11:15:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use CResources_Helpers_File as File;
use CResources_File as PendingFile;

class CModel_HasResource_FileAdder_FileAdder {

    /** @var CModel subject */
    protected $subject;

    /** @var \Spatie\ResourceLibrary\Filesystem\Filesystem */
    protected $filesystem;

    /** @var bool */
    protected $preserveOriginal = false;

    /** @var string|\Symfony\Component\HttpFoundation\File\UploadedFile */
    protected $file;

    /** @var array */
    protected $properties = [];

    /** @var array */
    protected $customProperties = [];

    /** @var array */
    protected $manipulations = [];

    /** @var string */
    protected $pathToFile;

    /** @var string */
    protected $fileName;

    /** @var string */
    protected $resourceName;

    /** @var string */
    protected $diskName = '';

    /** @var null|callable */
    protected $fileNameSanitizer;

    /** @var bool */
    protected $generateResponsiveImages = false;

    /** @var array */
    protected $customHeaders = [];

    /**
     * @param Filesystem $fileSystem
     */
    public function __construct(CResources_Filesystem $fileSystem) {
        $this->filesystem = $fileSystem;
        $this->fileNameSanitizer = function ($fileName) {
            return $this->defaultSanitizer($fileName);
        };
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $subject
     *
     * @return FileAdder
     */
    public function setSubject(CModel $subject) {
        $this->subject = $subject;
        return $this;
    }

    /*
     * Set the file that needs to be imported.
     *
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return $this
     */

    public function setFile($file) {
        $this->file = $file;
        if (is_string($file)) {
            $this->pathToFile = $file;
            $this->setFileName(pathinfo($file, PATHINFO_BASENAME));
            $this->resourceName = pathinfo($file, PATHINFO_FILENAME);
            return $this;
        }
        if ($file instanceof UploadedFile) {
            $this->pathToFile = $file->getPath() . '/' . $file->getFilename();
            $this->setFileName($file->getClientOriginalName());
            $this->resourceName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            return $this;
        }
        if ($file instanceof SymfonyFile) {
            $this->pathToFile = $file->getPath() . '/' . $file->getFilename();
            $this->setFileName(pathinfo($file->getFilename(), PATHINFO_BASENAME));
            $this->resourceName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            return $this;
        }
        throw UnknownType::create();
    }

    public function preservingOriginal() {
        $this->preserveOriginal = true;
        return $this;
    }

    public function usingName($name) {
        return $this->setName($name);
    }

    public function setName($name) {
        $this->resourceName = $name;
        return $this;
    }

    public function usingFileName($fileName) {
        return $this->setFileName($fileName);
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
        return $this;
    }

    public function withCustomProperties(array $customProperties) {
        $this->customProperties = $customProperties;
        return $this;
    }

    public function withManipulations(array $manipulations) {
        $this->manipulations = $manipulations;
        return $this;
    }

    public function withProperties(array $properties) {
        $this->properties = $properties;
        return $this;
    }

    public function withAttributes(array $properties) {
        return $this->withProperties($properties);
    }

    public function withResponsiveImages() {
        $this->generateResponsiveImages = true;
        return $this;
    }

    public function addCustomHeaders(array $customRemoteHeaders) {
        $this->customHeaders = $customRemoteHeaders;
        $this->filesystem->addCustomRemoteHeaders($customRemoteHeaders);
        return $this;
    }

    public function toResourceCollectionOnCloudDisk($collectionName = 'default') {
        return $this->toResourceCollection($collectionName, config('filesystems.cloud'));
    }

    public function toResourceCollection($collectionName = 'default', $diskName = '') {
       
        if (!is_file($this->pathToFile)) {
            throw CResources_Exception_FileCannotBeAdded_FileDoesNotExist::create($this->pathToFile);
        }
        $maxFileSize = CF::config('resource.max_file_size');
        if ($maxFileSize !== null) {
            if (filesize($this->pathToFile) > CF::config('resource.max_file_size')) {
                throw CResources_Exception_FileCannotBeAdded_FileIsTooBig::create($this->pathToFile);
            }
        }

        $resourceClass = CF::config('resource.resource_model');
        if ($resourceClass == null) {
            $resourceClass = 'CApp_Model_Resource';
        }
        /** @var CApp_Model_Resource $resource */
        $resource = new $resourceClass();
        $resource->name = $this->resourceName;
        $this->fileName = call_user_func($this->fileNameSanitizer, $this->fileName);
        $resource->file_name = $this->fileName;
        $resource->disk = $this->determineDiskName($diskName, $collectionName);

        $resource->collection_name = $collectionName;
        $resource->mime_type = File::getMimetype($this->pathToFile);
        $resource->size = filesize($this->pathToFile);
        $resource->custom_properties = $this->customProperties;
        $resource->responsive_images = [];
        $resource->manipulations = $this->manipulations;
        if (CF::filled($this->customHeaders)) {
            $resource->setCustomHeaders($this->customHeaders);
        }
        $resource->fill($this->properties);
        $this->attachResource($resource);
        return $resource;
    }

    protected function determineDiskName($diskName, $collectionName) {
        if ($diskName !== '') {
            return $diskName;
        }
        if ($collection = $this->getResourceCollection($collectionName)) {
            $collectionDiskName = $collection->diskName;
            if ($collectionDiskName !== '') {
                return $collectionDiskName;
            }
        }
        return CF::config('resource.disk');
    }

    public function defaultSanitizer($fileName) {
        return str_replace(['#', '/', '\\', ' '], '-', $fileName);
    }

    public function sanitizingFileName(callable $fileNameSanitizer) {
        $this->fileNameSanitizer = $fileNameSanitizer;
        return $this;
    }

    protected function attachResource(CApp_Model_Interface_ResourceInterface $resource) {
        if (!$this->subject->exists) {
            $this->subject->prepareToAttachResource($resource, $this);
            $class = get_class($this->subject);
            $class::created(function ($model) {
                $model->processUnattachedResource(function (CApp_Model_Interface_ResourceInterface $resource, FileAdder $fileAdder) use ($model) {
                    $this->processResourceItem($model, $resource, $fileAdder);
                });
            });
            return;
        }
        $this->processResourceItem($this->subject, $resource, $this);
    }

    protected function processResourceItem(CModel_HasResourceInterface $model, CApp_Model_Interface_ResourceInterface $resource, self $fileAdder) {
        $this->guardAgainstDisallowedFileAdditions($resource, $model);
        $model->resource()->save($resource);
        $this->filesystem->add($fileAdder->pathToFile, $resource, $fileAdder->fileName);
        if (!$fileAdder->preserveOriginal) {
            unlink($fileAdder->pathToFile);
        }
        if ($this->generateResponsiveImages && (new ImageGenerator())->canConvert($resource)) {
            $generateResponsiveImagesJobClass = CF::config('resource.jobs.generate_responsive_images', GenerateResponsiveImages::class);
            $job = new $generateResponsiveImagesJobClass($resource);
            if ($customQueue = CF::config('resource.queue_name')) {
                $job->onQueue($customQueue);
            }
            dispatch($job);
        }
        if ($collectionSizeLimit = COptional::create($this->getResourceCollection($resource->collection_name))->collectionSizeLimit) {
            $collectionResource = $this->subject->fresh()->getResource($resource->collection_name);
            
            if ($collectionResource->count() > $collectionSizeLimit) {
                $model->clearResourceCollectionExcept($resource->collection_name, $collectionResource->reverse()->take($collectionSizeLimit));
            }
        }
        
    }

    protected function getResourceCollection($collectionName) {
        $this->subject->registerResourceCollections();
        return CF::collect($this->subject->resourceCollections)
                        ->first(function (CResources_ResourceCollection $collection) use ($collectionName) {
                            return $collection->name === $collectionName;
                        });
    }

    protected function guardAgainstDisallowedFileAdditions(CApp_Model_Interface_ResourceInterface $resource) {
        $file = PendingFile::createFromResource($resource);
        if (!$collection = $this->getResourceCollection($resource->collection_name)) {
            return;
        }
        if (!call_user_func($collection->acceptsFile, $file, $this->subject)) {
            throw CResources_Exception_FileCannotBeAdded_FileUnacceptableForCollection::create($file, $collection, $this->subject);
        }
    }

}
