<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CModel_HasResource_HasResourceTrait {

    /** @var array */
    public $resourceConversions = [];

    /** @var array */
    public $resourceCollections = [];

    /** @var bool */
    protected $deletePreservingResource = false;

    /** @var array */
    protected $unAttachedResourceLibraryItems = [];

    public static function bootHasResourceTrait() {
        static::deleting(function (CModel_HasResourceInterface $entity) {
            if ($entity->shouldDeletePreservingResource()) {
                return;
            }
            if (in_array(CModel_SoftDelete_SoftDeleteTrait::class, CF::class_uses_recursive($entity))) {
                if (!$entity->forceDeleting) {
                    return;
                }
            }
            $entity->resource()->get()->each->delete();
        });
    }

    /**
     * Set the polymorphic relation.
     *
     * @return CModel_Relation_MorphMany
     */
    public function resource() {
        $resourceModel = CF::config('resource.resource_model', CApp_Model_Resource::class);
        return $this->morphMany($resourceModel, 'model');
    }

    /**
     * Add a file to the resourcelibrary.
     *
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return CModel_HasResource_FileAdder_FileAdder
     */
    public function addResource($file) {
        return CModel_HasResource_FileAdder_FileAdderFactory::create($this, $file);
    }

    /**
     * Add a file from the given disk.
     *
     * @param string $key
     * @param string $disk
     *
     * @return CModel_HasResource_FileAdder_FileAdder
     */
    public function addResourceFromDisk( $key,  $disk = null)
    {
        return CModel_HasResource_FileAdder_FileAdderFactory::createFromDisk($this, $key, $disk ?: CF::config('storage.default'));
    }
    
    /**
     * Add a file from a request.
     *
     * @param string $key
     *
     * @return CModel_HasResource_FileAdder_FileAdder
     */
    public function addResourceFromRequest($key) {
        return CModel_HasResource_FileAdder_FileAdderFactory::createFromRequest($this, $key);
    }

    /**
     * Add multiple files from a request by keys.
     *
     * @param string[] $keys
     *
     * @return CModel_HasResource_FileAdder_FileAdder[]
     */
    public function addMultipleResourceFromRequest(array $keys) {
        return CModel_HasResource_FileAdder_FileAdderFactory::createMultipleFromRequest($this, $keys);
    }

    /**
     * Add all files from a request.
     *
     * @return CModel_HasResource_FileAdder_FileAdder[]
     */
    public function addAllResourceFromRequest() {
        return CModel_HasResource_FileAdder_FileAdderFactory::createAllFromRequest($this);
    }

    /**
     * Add a remote file to the resourcelibrary.
     *
     * @param string $url
     * @param string|array ...$allowedMimeTypes
     *
     * @return CModel_HasResource_FileAdder_FileAdder
     *
     * @throws CResources_Exception_FileCannotBeAdded
     */
    public function addResourceFromUrl() {
        $args = func_get_args();
        $url = carr::get($args, 0);
        $allowedMimeTypes = array_slice($args, 1);
        if (!$stream = @fopen($url, 'r')) {
            throw CResources_Exception_FileCannotBeAdded_UnreachableUrl::create($url);
        }
        $temporaryFile = tempnam(sys_get_temp_dir(), 'resource-library');
        file_put_contents($temporaryFile, $stream);
        $this->guardAgainstInvalidMimeType($temporaryFile, $allowedMimeTypes);
        $filename = basename(parse_url($url, PHP_URL_PATH));
        $filename = str_replace('%20', ' ', $filename);
        if ($filename === '') {
            $filename = 'file';
        }
        $resourceExtension = explode('/', mime_content_type($temporaryFile));
        if (!cstr::contains($filename, '.')) {
            $filename = "{$filename}.{$resourceExtension[1]}";
        }
        $file= CModel_HasResource_FileAdder_FileAdderFactory::create($this, $temporaryFile)
                ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                        ->usingFileName($filename);;
        return $file;
    }

    /**
     * Add a base64 encoded file to the resourcelibrary.
     *
     * @param string $base64data
     * @param string|array ...$allowedMimeTypes
     *
     * @throws InvalidBase64Data
     * @throws CResources_Exception_FileCannotBeAdded
     *
     * @return CModel_HasResource_FileAdder_FileAdder
     */
    public function addResourceFromBase64() {
        $args = func_get_args();
        $base64data = carr::get($args, 0);
        $allowedMimeTypes = array_slice($args, 1);
        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            $base64data = carr::get(explode(';', $base64data), 1);
            $base64data = carr::get(explode(',', $base64data), 1);
        }
        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            throw InvalidBase64Data::create();
        }
        // decoding and then reencoding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            throw InvalidBase64Data::create();
        }
        $binaryData = base64_decode($base64data);
        // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder
        $tmpFile = tempnam(sys_get_temp_dir(), 'resourcelibrary');
        file_put_contents($tmpFile, $binaryData);
        $this->guardAgainstInvalidMimeType($tmpFile, $allowedMimeTypes);
        $file= CModel_HasResource_FileAdder_FileAdderFactory::create($this, $tmpFile);
       
        return $file;
    }

    /**
     * Copy a file to the resourcelibrary.
     *
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return CModel_HasResource_FileAdder_FileAdder
     */
    public function copyResource($file) {
        return $this->addResource($file)->preservingOriginal();
    }

    /*
     * Determine if there is resource in the given collection.
     */

    public function hasResource($collectionName = 'default') {
        return count($this->getResource($collectionName)) ? true : false;
    }

    /**
     * Get resource collection by its collectionName.
     *
     * @param string $collectionName
     * @param array|callable $filters
     *
     * @return CCollection
     */
    public function getResource($collectionName = 'default', $filters = []) {
        $repository = new CResources_Repository();
        
        return $repository->getCollection($this, $collectionName, $filters);
    }

    public function getFirstResource($collectionName = 'default', array $filters = []) {
        $resource = $this->getResource($collectionName, $filters);
        return $resource->first();
    }

    /*
     * Get the url of the image for the given conversionName
     * for first resource for the given collectionName.
     * If no profile is given, return the source's url.
     */

    public function getFirstResourceUrl($collectionName = 'default', $conversionName = '') {
        $resource = $this->getFirstResource($collectionName);
        if (!$resource) {
            return '';
        }
        return $resource->getUrl($conversionName);
    }
    
    /*
     * Get the url of the image for the given conversionName
     * for first resource for the given collectionName.
     * If no profile is given, return the source's full url.
     */

    public function getFirstResourceFullUrl($collectionName = 'default', $conversionName = '') {
        $resource = $this->getFirstResource($collectionName);
        if (!$resource) {
            return '';
        }
        return $resource->getFullUrl($conversionName);
    }

    /*
     * Get the url of the image for the given conversionName
     * for first resource for the given collectionName.
     * If no profile is given, return the source's url.
     */

    public function getFirstTemporaryUrl(DateTimeInterface $expiration, $collectionName = 'default', $conversionName = '') {
        $resource = $this->getFirstResource($collectionName);
        if (!$resource) {
            return '';
        }
        return $resource->getTemporaryUrl($expiration, $conversionName);
    }

    /*
     * Get the url of the image for the given conversionName
     * for first resource for the given collectionName.
     * If no profile is given, return the source's url.
     */

    public function getFirstResourcePath($collectionName = 'default', $conversionName = '') {
        $resource = $this->getFirstResource($collectionName);
        if (!$resource) {
            return '';
        }
        return $resource->getPath($conversionName);
    }

    /**
     * Update a resource collection by deleting and inserting again with new values.
     *
     * @param array $newResourceArray
     * @param string $collectionName
     *
     * @return CCollection
     *
     * @throws \Spatie\ResourceLibrary\Exceptions\ResourceCannotBeUpdated
     */
    public function updateResource(array $newResourceArray, $collectionName = 'default') {
        $this->removeResourceItemsNotPresentInArray($newResourceArray, $collectionName);
        return CF::collect($newResourceArray)
                        ->map(function (array $newResourceItem) use ($collectionName) {
                            static $orderColumn = 1;
                            $resourceClass = config('resourcelibrary.resource_model');
                            $currentResource = $resourceClass::findOrFail($newResourceItem['id']);
                            if ($currentResource->collection_name !== $collectionName) {
                                throw ResourceCannotBeUpdated::doesNotBelongToCollection($collectionName, $currentResource);
                            }
                            if (array_key_exists('name', $newResourceItem)) {
                                $currentResource->name = $newResourceItem['name'];
                            }
                            if (array_key_exists('custom_properties', $newResourceItem)) {
                                $currentResource->custom_properties = $newResourceItem['custom_properties'];
                            }
                            $currentResource->order_column = $orderColumn++;
                            $currentResource->save();
                            return $currentResource;
                        });
    }

    protected function removeResourceItemsNotPresentInArray(array $newResourceArray, $collectionName = 'default') {
        $this->getResource($collectionName)
                ->reject(function (Resource $currentResourceItem) use ($newResourceArray) {
                    return in_array($currentResourceItem->id, array_column($newResourceArray, 'id'));
                })
        ->each->delete();
    }

    /**
     * Remove all resource in the given collection.
     *
     * @param string $collectionName
     *
     * @return $this
     */
    public function clearResourceCollection($collectionName = 'default') {
        $this->getResource($collectionName)->each->delete();
        event(new CollectionHasBeenCleared($this, $collectionName));
        if ($this->resourceIsPreloaded()) {
            unset($this->resource);
        }
        return $this;
    }

    /**
     * Remove all resource in the given collection except some.
     *
     * @param string $collectionName
     * @param \Spatie\ResourceLibrary\Models\Resource[]|\Illuminate\Support\Collection $excludedResource
     *
     * @return $this
     */
    public function clearResourceCollectionExcept($collectionName = 'default', $excludedResource = []) {
        if ($excludedResource instanceof CApp_Model_Interface_ResourceInterface) {
            $excludedResource = CF::collect()->push($excludedResource);
        }
        $excludedResource = CF::collect($excludedResource);
        
        if ($excludedResource->isEmpty()) {
            return $this->clearResourceCollection($collectionName);
        }
       
        $this->getResource($collectionName)
                ->reject(function (CApp_Model_Interface_ResourceInterface $resource) use ($excludedResource) {
                    
                    return $excludedResource->where('resource_id', $resource->resource_id)->count();
                })
        ->each->delete();
        if ($this->resourceIsPreloaded()) {
            unset($this->resource);
        }
        return $this;
    }

    /**
     * Delete the associated resource with the given id.
     * You may also pass a resource object.
     *
     * @param int|\Spatie\ResourceLibrary\Models\Resource $resourceId
     *
     * @throws \Spatie\ResourceLibrary\Exceptions\ResourceCannotBeDeleted
     */
    public function deleteResource($resourceId) {
        if ($resourceId instanceof CApp_Model_Interface_ResourceInterface) {
            $resourceId = $resourceId->id;
        }
        $resource = $this->resource->find($resourceId);
        if (!$resource) {
            throw ResourceCannotBeDeleted::doesNotBelongToModel($resourceId, $this);
        }
        $resource->delete();
    }

    /*
     * Add a conversion.
     */

    public function addResourceConversion($name) {
        $conversion = CResources_Conversion::create($name);
        $this->resourceConversions[] = $conversion;
        return $conversion;
    }

    public function addResourceCollection($name) {
        $resourceCollection = CResources_ResourceCollection::create($name);
        $this->resourceCollections[] = $resourceCollection;
        return $resourceCollection;
    }

    /**
     * Delete the model, but preserve all the associated resource.
     *
     * @return bool
     */
    public function deletePreservingResource() {
        $this->deletePreservingResource = true;
        return $this->delete();
    }

    /**
     * Determines if the resource files should be preserved when the resource object gets deleted.
     *
     * @return bool
     */
    public function shouldDeletePreservingResource() {
        return $this->deletePreservingResource ? $this->deletePreservingResource : false;
    }

    protected function resourceIsPreloaded() {
        return $this->relationLoaded('resource');
    }

    /**
     * Cache the resource on the object.
     *
     * @param string $collectionName
     *
     * @return mixed
     */
    public function loadResource($collectionName) {
        $collection = $this->exists ? $this->resource : CF::collect($this->unAttachedResourceLibraryItems)->pluck('resource');
        return $collection
                        ->filter(function (CApp_Model_Interface_ResourceInterface $resourceItem) use ($collectionName) {
                            if ($collectionName == '') {
                                return true;
                            }
                            return $resourceItem->collection_name === $collectionName;
                        })
                        ->sortBy('order_column')
                        ->values();
    }

    public function prepareToAttachResource(CApp_Model_Interface_ResourceInterface $resource, CModel_HasResource_FileAdder_FileAdder $fileAdder) {
        $this->unAttachedResourceLibraryItems[] = compact('resource', 'fileAdder');
    }

    public function processUnattachedResource(callable $callable) {
        foreach ($this->unAttachedResourceLibraryItems as $item) {
            $callable($item['resource'], $item['fileAdder']);
        }
        $this->unAttachedResourceLibraryItems = [];
    }

    /**
     * 
     * @param string $file
     * @param string ..$allowedMimeTypes
     * @return type
     * @throws type
     */
    protected function guardAgainstInvalidMimeType() {
        $args = func_get_args();
        $file = carr::get($args, 0);
        $allowedMimeTypes = array_slice($args, 1);
        $allowedMimeTypes = carr::flatten($allowedMimeTypes);
        if (empty($allowedMimeTypes)) {
            return;
        }
        $validation = Validator::make(
                        ['file' => new File($file)], ['file' => 'mimetypes:' . implode(',', $allowedMimeTypes)]
        );
        if ($validation->fails()) {
            throw MimeTypeNotAllowed::create($file, $allowedMimeTypes);
        }
    }

    public function registerResourceConversions(CApp_Model_Interface_ResourceInterface $resource = null) {
        
    }

    public function registerResourceCollections() {
        
    }

    public function registerAllResourceConversions(CApp_Model_Interface_ResourceInterface $resource = null) {
        $this->registerResourceCollections();
        CF::collect($this->resourceCollections)->each(function (CResources_ResourceCollection $resourceCollection) use ($resource) {
            $actualResourceConversions = $this->resourceConversions;
            $this->resourceConversions = [];
            call_user_func_array($resourceCollection->resourceConversionRegistrations, array($resource));

            $preparedResourceConversions = CF::collect($this->resourceConversions)
                    ->each(function (CResources_Conversion $conversion) use ($resourceCollection) {
                        $conversion->performOnCollections($resourceCollection->name);
                    })
                    ->values()
                    ->toArray();
            $this->resourceConversions = array_merge($actualResourceConversions, $preparedResourceConversions);
        });
        $this->registerResourceConversions($resource);
    }

}
