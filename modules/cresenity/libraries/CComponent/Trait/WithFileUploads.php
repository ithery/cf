<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 30, 2020 
 * @license Ittron Global Teknologi
 */
trait CComponent_Trait_WithFileUploads {

    public function startUpload($name, $fileInfo, $isMultiple) {
        if (CComponent_FileUploadConfiguration::isUsingS3()) {
            c::throwIf($isMultiple, CComponent_Exception_S3DoesntSupportMultipleFileUploads::class);

            $file = CHTTP_UploadedFile::fake()->create($fileInfo[0]['name'], $fileInfo[0]['size'] / 1024, $fileInfo[0]['type']);

            $this->emit('upload:generatedSignedUrlForS3', $name, CComponent_GenerateSignedUploadUrl::forS3($file))->self();

            return;
        }

        $this->emit('upload:generatedSignedUrl', $name, CComponent_GenerateSignedUploadUrl::forLocal())->self();
    }

    public function finishUpload($name, $tmpPath, $isMultiple) {
        $this->cleanupOldUploads();
        
        if ($isMultiple) {
            $file = c::collect($tmpPath)->map(function ($i) {
                        return CComponent_TemporaryUploadedFile::createFromComponent($i);
                    })->toArray();
            $this->emit('upload:finished', $name, c::collect($file)->map->getFilename()->toArray())->self();
        } else {
            $file = CComponent_TemporaryUploadedFile::createFromComponent($tmpPath[0]);
            $this->emit('upload:finished', $name, [$file->getFilename()])->self();

            // If the property is an array, but the upload ISNT set to "multiple"
            // then APPEND the upload to the array, rather than replacing it.
            if (is_array($value = $this->getPropertyValue($name))) {
                $file = array_merge($value, [$file]);
            }
        }

        $this->syncInput($name, $file);
    }

    public function uploadErrored($name, $errorsInJson, $isMultiple) {
        $this->emit('upload:errored', $name)->self();

        if (is_null($errorsInJson)) {
            $genericValidationMessage = c::trans('validation.uploaded', ['attribute' => $name]);
            if ($genericValidationMessage === 'validation.uploaded')
                $genericValidationMessage = "The {$name} failed to upload.";
            throw CValidation_Exception::withMessages([$name => $genericValidationMessage]);
        }

        $errorsInJson = $isMultiple ? str_ireplace('files', $name, $errorsInJson) : str_ireplace('files.0', $name, $errorsInJson);

        $errors = json_decode($errorsInJson, true)['errors'];

        throw (CValidation_Exception::withMessages($errors));
    }

    public function removeUpload($name, $tmpFilename) {
        $uploads = $this->getPropertyValue($name);

        if (is_array($uploads) && isset($uploads[0]) && $uploads[0] instanceof TemporaryUploadedFile) {
            $this->emit('upload:removed', $name, $tmpFilename)->self();

            $this->syncInput($name, array_values(array_filter($uploads, function ($upload) use ($tmpFilename) {
                                if ($upload->getFilename() === $tmpFilename) {
                                    $upload->delete();
                                    return false;
                                }

                                return true;
                            })));
        } elseif ($uploads instanceof CComponent_TemporaryUploadedFile) {
            $uploads->delete();

            $this->emit('upload:removed', $name, $tmpFilename)->self();

            if ($uploads->getFilename() === $tmpFilename)
                $this->syncInput($name, null);
        }
    }

    protected function cleanupOldUploads() {
        if (CComponent_FileUploadConfiguration::isUsingS3())
            return;

        $storage = CComponent_FileUploadConfiguration::storage();

        foreach ($storage->allFiles(CComponent_FileUploadConfiguration::path()) as $filePathname) {
            $yesterdaysStamp = c::now()->subDay()->timestamp;
            if ($yesterdaysStamp > $storage->lastModified($filePathname)) {
                $storage->delete($filePathname);
            }
        }
    }

}
