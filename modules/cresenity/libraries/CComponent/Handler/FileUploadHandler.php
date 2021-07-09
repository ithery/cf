<?php

/**
 * Description of FileUploadHandler
 *
 * @author Hery
 */
class CComponent_Handler_FileUploadHandler extends CComponent_HandlerAbstract {

    public function getMiddleware() {
        return [[
        'middleware' => CComponent_FileUploadConfiguration::middleware(),
        'options' => [],
        ]];
    }

    public function handle() {
        //TODO: check signature
        //c::abortUnless(CHTTP::request()->hasValidSignature(), 401);

        $disk = CComponent_FileUploadConfiguration::disk();

        $filePaths = $this->validateAndStore(c::request('files'), $disk);

        return ['paths' => $filePaths];
    }

    public function validateAndStore($files, $disk) {
       
        c::validator()->make(['files' => $files], [
            'files.*' => CComponent_FileUploadConfiguration::rules()
        ])->validate();

        $fileHashPaths = c::collect($files)->map(function ($file) use ($disk) {
            $filename = CComponent_TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($file);
            
            return $file->storeAs('/' . CComponent_FileUploadConfiguration::path(), $filename, [
                        'disk' => $disk
            ]);
        });

        // Strip out the temporary upload directory from the paths.
        return $fileHashPaths->map(function ($path) {
                    return str_replace(CComponent_FileUploadConfiguration::path('/'), '', $path);
                });
    }

    public function __invoke() {
        return $this->handle();
    }

}
