<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 1, 2019, 11:36:15 PM
 */
class CResources_Filesystem {
    /**
     * @var CResources_Manager
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $customRemoteHeaders = [];

    public function __construct($filesystem = null) {
        if ($filesystem == null) {
            $filesystem = CStorage::instance();
        }
        $this->filesystem = $filesystem;
    }

    public function add($file, CModel_Resource_ResourceInterface $resource, $targetFileName = null) {
        $this->copyToResourceLibrary($file, $resource, null, $targetFileName);
        CEvent::dispatch(new CResources_Event_ResourceHasBeenAdded($resource));
        CResources_Factory::createFileManipulator()->createDerivedFiles($resource);
    }

    public function copyToResourceLibrary($pathToFile, CModel_Resource_ResourceInterface $resource, $type = null, $targetFileName = null) {
        $destinationFileName = $targetFileName ?: pathinfo($pathToFile, PATHINFO_BASENAME);
        $destination = $this->getResourceDirectory($resource, $type) . $destinationFileName;

        $file = fopen($pathToFile, 'r');
        if ($resource->getDiskDriverName() === 'local') {
            $this->filesystem
                ->disk($resource->disk)
                ->put($destination, $file);
            fclose($file);

            return;
        }

        $this->filesystem
            ->disk($resource->disk)
            ->put($destination, $file, $this->getRemoteHeadersForFile($pathToFile, $resource->getCustomHeaders()));
        if (is_resource($file)) {
            fclose($file);
        }
    }

    public function addCustomRemoteHeaders(array $customRemoteHeaders) {
        $this->customRemoteHeaders = $customRemoteHeaders;
    }

    public function getRemoteHeadersForFile($file, array $resourceCustomHeaders = []) {
        $mimeTypeHeader = ['ContentType' => CResources_Helpers_File::getMimeType($file)];
        $extraHeaders = CF::config('resource.remote.extra_headers');

        return array_merge($mimeTypeHeader, $extraHeaders, $this->customRemoteHeaders, $resourceCustomHeaders);
    }

    public function getStream(CModel_Resource_ResourceInterface $resource) {
        $sourceFile = $this->getResourceDirectory($resource) . '/' . $resource->file_name;

        return $this->filesystem->disk($resource->disk)->readStream($sourceFile);
    }

    public function copyFromResourceLibrary(CModel_Resource_ResourceInterface $resource, $targetFile) {
        touch($targetFile);
        $stream = $this->getStream($resource);
        $targetFileStream = fopen($targetFile, 'a');
        while (!feof($stream)) {
            $chunk = fread($stream, 1024);
            fwrite($targetFileStream, $chunk);
        }
        fclose($stream);
        fclose($targetFileStream);

        return $targetFile;
    }

    public function removeAllFiles(CModel_Resource_ResourceInterface $resource) {
        $resourceDirectory = $this->getResourceDirectory($resource);
        $conversionsDirectory = $this->getResourceDirectory($resource, 'conversions');
        $responsiveImagesDirectory = $this->getResourceDirectory($resource, 'responsiveImages');
        c::collect([$resourceDirectory, $conversionsDirectory, $responsiveImagesDirectory])
            ->each(function ($directory) use ($resource) {
                $this->filesystem->disk($resource->disk)->deleteDirectory($directory);
            });
    }

    public function removeFile(CModel_Resource_ResourceInterface $resource, $path) {
        $this->filesystem->disk($resource->disk)->delete($path);
    }

    public function removeResponsiveImages(CModel_Resource_ResourceInterface $resource, $conversionName = 'resource_original') {
        $responsiveImagesDirectory = $this->getResponsiveImagesDirectory($resource);
        $allFilePaths = $this->filesystem->allFiles($responsiveImagesDirectory);
        $responsiveImagePaths = array_filter($allFilePaths, function ($path) use ($conversionName) {
            return cstr::contains($path, $conversionName);
        });
        $this->filesystem->delete($responsiveImagePaths);
    }

    public function syncFileNames(CModel_Resource_ResourceInterface $resource) {
        $this->renameResourceFile($resource);
        $this->renameConversionFiles($resource);
    }

    protected function renameResourceFile(CModel_Resource_ResourceInterface $resource) {
        /** @var CModel|CModel_Resource_ResourceInterface $resource */
        $newFileName = $resource->file_name;
        $oldFileName = $resource->getOriginal('file_name');
        $resourceDirectory = $this->getResourceDirectory($resource);
        $oldFile = $resourceDirectory . '/' . $oldFileName;
        $newFile = $resourceDirectory . '/' . $newFileName;
        $this->filesystem->disk($resource->disk)->move($oldFile, $newFile);
    }

    protected function renameConversionFiles(CModel_Resource_ResourceInterface $resource) {
        $newFileName = $resource->file_name;
        /** @var CModel|CModel_Resource_ResourceInterface $resource */
        $oldFileName = $resource->getOriginal('file_name');
        $conversionDirectory = $this->getConversionDirectory($resource);
        $conversionCollection = CResources_ConversionCollection::createForResource($resource);
        foreach ($resource->getResourceConversionNames() as $conversionName) {
            $conversion = $conversionCollection->getByName($conversionName);
            $oldFile = $conversionDirectory . $conversion->getConversionFile($oldFileName);
            $newFile = $conversionDirectory . $conversion->getConversionFile($newFileName);
            $disk = $this->filesystem->disk($resource->disk);
            // A resource conversion file might be missing, waiting to be generated, failed etc.
            if (!$disk->exists($oldFile)) {
                continue;
            }
            $disk->move($oldFile, $newFile);
        }
    }

    public function getResourceDirectory(CModel_Resource_ResourceInterface $resource, $type = null) {
        $pathGenerator = CResources_Factory::createPathGenerator();
        if (!$type) {
            $directory = $pathGenerator->getPath($resource);
        }
        if ($type === 'conversions') {
            $directory = $pathGenerator->getPathForConversions($resource);
        }
        if ($type === 'responsiveImages') {
            $directory = $pathGenerator->getPathForResponsiveImages($resource);
        }
        if (!in_array($resource->getDiskDriverName(), ['s3'], true)) {
            $this->filesystem->disk($resource->disk)->makeDirectory($directory);
        }

        return $directory;
    }

    public function getConversionDirectory(CModel_Resource_ResourceInterface $resource) {
        return $this->getResourceDirectory($resource, 'conversions');
    }

    public function getResponsiveImagesDirectory(CModel_Resource_ResourceInterface $resource) {
        return $this->getResourceDirectory($resource, 'responsiveImages');
    }
}
