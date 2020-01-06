<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 2:21:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CResources_Helpers_File as ResourceLibraryFileHelper;

class CResources_FileManipulator {

    /**
     * Create all derived files for the given resource.
     *
     * @param CApp_Model_Interface_ResourceInterface $resource
     * @param array $only
     * @param bool $onlyIfMissing
     */
    public function createDerivedFiles(CApp_Model_Interface_ResourceInterface $resource, array $only = [], $onlyIfMissing = false) {
        $profileCollection = CResources_ConversionCollection::createForResource($resource);
        if (!empty($only)) {
            $profileCollection = $profileCollection->filter(function ($collection) use ($only) {
                return in_array($collection->getName(), $only);
            });
        }
        $this->performConversions(
                $profileCollection->getNonQueuedConversions($resource->collection_name), $resource, $onlyIfMissing
        );
        $queuedConversions = $profileCollection->getQueuedConversions($resource->collection_name);
        if ($queuedConversions->isNotEmpty()) {
            $this->dispatchQueuedConversions($resource, $queuedConversions);
        }
    }

    /**
     * Perform the given conversions for the given resource.
     *
     * @param CResources_ConversionCollection $conversions
     * @param CApp_Model_Interface_ResourceInterface $resource
     * @param bool $onlyIfMissing
     */
    public function performConversions(CResources_ConversionCollection $conversions, CApp_Model_Interface_ResourceInterface $resource, $onlyIfMissing = false) {
        if ($conversions->isEmpty()) {
            return;
        }
        $imageGenerator = $this->determineImageGenerator($resource);
        if (!$imageGenerator) {
            return;
        }

        $resourceFileSystem = CResources_Factory::createFileSystem();
        $temporaryDirectoryPath = CResources_Helpers_TemporaryDirectory::generateLocalFilePath($resource->getExtensionAttribute());
        $copiedOriginalFile = $resourceFileSystem->copyFromResourceLibrary(
                $resource, $temporaryDirectoryPath
        );
        $conversions
                ->reject(function (CResources_Conversion $conversion) use ($onlyIfMissing, $resource) {
                    $relativePath = $resource->getPath($conversion->getName());
                    $rootPath = CF::config('storage.disks.' . $resource->disk . '.root');
                    if ($rootPath) {
                        $relativePath = str_replace($rootPath, '', $relativePath);
                    }
                    return $onlyIfMissing && Storage::disk($resource->disk)->exists($relativePath);
                })
                ->each(function (CResources_Conversion $conversion) use ($resource, $imageGenerator, $copiedOriginalFile) {
                    CEvent::dispatcher()->dispatch(new CResources_Event_Conversion_WillStart($resource, $conversion, $copiedOriginalFile));

                    $copiedOriginalFile = $imageGenerator->convert($copiedOriginalFile, $conversion);
                    $manipulationResult = $this->performManipulations($resource, $conversion, $copiedOriginalFile);
                    $newFileName = pathinfo($resource->file_name, PATHINFO_FILENAME) .
                            '-' . $conversion->getName() .
                            '.' . $conversion->getResultExtension(pathinfo($copiedOriginalFile, PATHINFO_EXTENSION));
                    $renamedFile = ResourceLibraryFileHelper::renameInDirectory($manipulationResult, $newFileName);
                    if ($conversion->shouldGenerateResponsiveImages()) {
                        app(ResponsiveImageGenerator::class)->generateResponsiveImagesForConversion(
                                $resource, $conversion, $renamedFile
                        );
                    }
                    CResources_Factory::createFileSystem()->copyToResourceLibrary($renamedFile, $resource, 'conversions');
                    $resource->markAsConversionGenerated($conversion->getName(), true);
                    CEvent::dispatcher()->dispatch(new CResources_Event_Conversion_ConversionHasBeenCompleted($resource, $conversion));
                });

        CResources_Helpers_TemporaryDirectory::delete($temporaryDirectoryPath);
    }

    public function performManipulations(CApp_Model_Interface_ResourceInterface $resource, CResources_Conversion $conversion, $imageFile) {
        if ($conversion->getManipulations()->isEmpty()) {
            return $imageFile;
        }
        $conversionTempFile = pathinfo($imageFile, PATHINFO_DIRNAME) . '/' . cstr::random(16)
                . $conversion->getName()
                . '.'
                . $resource->getExtensionAttribute();
        CHelper::file()->copy($imageFile, $conversionTempFile);
        $supportedFormats = ['jpg', 'pjpg', 'png', 'gif'];
        if ($conversion->shouldKeepOriginalImageFormat() && in_array(strtolower($resource->getExtensionAttribute()), $supportedFormats)) {
            $conversion->format($resource->getExtensionAttribute());
        }
        CResources_Helpers_ImageFactory::load($conversionTempFile)
                ->manipulate($conversion->getManipulations())
                ->save();
        return $conversionTempFile;
    }

    protected function dispatchQueuedConversions(CApp_Model_Interface_ResourceInterface $resource, CResources_ConversionCollection $queuedConversions) {
        $performConversionsJobClass = CF::config('resource.task_queue.perform_conversions', CResources_TaskQueue_PerformConversions::class);
        $job = new $performConversionsJobClass($queuedConversions, $resource);
        if ($customQueue = CF::config('resource.queue_name')) {
            $job->onQueue($customQueue);
        }
        CQueue::dispatcher()->dispatch($job);
    }

    /**
     * @param CApp_Model_Interface_ResourceInterface $resource
     *
     * @return \Spatie\ResourceLibrary\ImageGenerators\ImageGenerator|null
     */
    public function determineImageGenerator(CApp_Model_Interface_ResourceInterface $resource) {
        return $resource->getImageGenerators()
                        ->map(function ( $imageGeneratorClassName) {
                            return CContainer::getInstance()->build($imageGeneratorClassName);
                        })
                        ->first(function (CResources_ImageGenerator_FileTypeAbstract $imageGenerator) use ($resource) {
                            return $imageGenerator->canConvert($resource);
                        });
    }

}
