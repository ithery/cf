<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 2:21:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
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
     * @param \Spatie\ResourceLibrary\Conversion\ConversionCollection $conversions
     * @param CApp_Model_Interface_ResourceInterface $resource
     * @param bool $onlyIfMissing
     */
    public function performConversions(ConversionCollection $conversions, CApp_Model_Interface_ResourceInterface $resource, $onlyIfMissing = false) {
        if ($conversions->isEmpty()) {
            return;
        }
        $imageGenerator = $this->determineImageGenerator($resource);
        if (!$imageGenerator) {
            return;
        }
        $temporaryDirectory = TemporaryDirectory::create();
        $copiedOriginalFile = app(Filesystem::class)->copyFromResourceLibrary(
                $resource, $temporaryDirectory->path(str_random(16) . '.' . $resource->extension)
        );
        $conversions
                ->reject(function (Conversion $conversion) use ($onlyIfMissing, $resource) {
                    $relativePath = $resource->getPath($conversion->getName());
                    $rootPath = config('filesystems.disks.' . $resource->disk . '.root');
                    if ($rootPath) {
                        $relativePath = str_replace($rootPath, '', $relativePath);
                    }
                    return $onlyIfMissing && Storage::disk($resource->disk)->exists($relativePath);
                })
                ->each(function (Conversion $conversion) use ($resource, $imageGenerator, $copiedOriginalFile) {
                    event(new ConversionWillStart($resource, $conversion, $copiedOriginalFile));
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
                    app(Filesystem::class)->copyToResourceLibrary($renamedFile, $resource, 'conversions');
                    $resource->markAsConversionGenerated($conversion->getName(), true);
                    event(new ConversionHasBeenCompleted($resource, $conversion));
                });
        $temporaryDirectory->delete();
    }

    public function performManipulations(CApp_Model_Interface_ResourceInterface $resource, Conversion $conversion, $imageFile) {
        if ($conversion->getManipulations()->isEmpty()) {
            return $imageFile;
        }
        $conversionTempFile = pathinfo($imageFile, PATHINFO_DIRNAME) . '/' . str_random(16)
                . $conversion->getName()
                . '.'
                . $resource->extension;
        File::copy($imageFile, $conversionTempFile);
        $supportedFormats = ['jpg', 'pjpg', 'png', 'gif'];
        if ($conversion->shouldKeepOriginalImageFormat() && in_array($resource->extension, $supportedFormats)) {
            $conversion->format($resource->extension);
        }
        ImageFactory::load($conversionTempFile)
                ->manipulate($conversion->getManipulations())
                ->save();
        return $conversionTempFile;
    }

    protected function dispatchQueuedConversions(Resource $resource, ConversionCollection $queuedConversions) {
        $performConversionsJobClass = config('resourcelibrary.jobs.perform_conversions', PerformConversions::class);
        $job = new $performConversionsJobClass($queuedConversions, $resource);
        if ($customQueue = config('resourcelibrary.queue_name')) {
            $job->onQueue($customQueue);
        }
        app(Dispatcher::class)->dispatch($job);
    }

    /**
     * @param CApp_Model_Interface_ResourceInterface $resource
     *
     * @return \Spatie\ResourceLibrary\ImageGenerators\ImageGenerator|null
     */
    public function determineImageGenerator(CApp_Model_Interface_ResourceInterface $resource) {
        return $resource->getImageGenerators()
                        ->map(function ( $imageGeneratorClassName) {
                            return app($imageGeneratorClassName);
                        })
                        ->first(function (ImageGenerator $imageGenerator) use ($resource) {
                            return $imageGenerator->canConvert($resource);
                        });
    }

}
