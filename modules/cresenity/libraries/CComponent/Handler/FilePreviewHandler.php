<?php

/**
 * Description of FilePreviewHandler
 *
 * @author Hery
 */
class CComponent_Handler_FilePreviewHandler {

    use CComponent_Handler_CanPretendToBeAFileTrait;

    public function handle($payload) {
        //c::abortUnless(c::request()->hasValidSignature(), 401);
        $filename = c::request('filename');
        
        return $this->pretendResponseIsFile(CComponent_FileUploadConfiguration::storage()->path(CComponent_FileUploadConfiguration::path($filename)));
    }

    public function __invoke($payload) {
       
        return $this->handle($payload);
    }

}
