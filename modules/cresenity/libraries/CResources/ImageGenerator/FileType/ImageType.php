<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_ImageGenerator_FileType_ImageType extends CResources_ImageGenerator_FileTypeAbstract {

    /**
     * 
     * @param string $path
     * @param CResources_Conversion $conversion
     * @return string
     */
    public function convert($path, CResources_Conversion $conversion = null) {
        return $path;
    }

    /**
     * 
     * @return bool
     */
    public function requirementsAreInstalled() {
        return true;
    }

    /**
     * 
     * @return CCollection
     */
    public function supportedExtensions() {
        return CF::collect(['png', 'jpg', 'jpeg', 'gif']);
    }

    /**
     * 
     * @return CCollection
     */
    public function supportedMimeTypes() {
        return CF::collect(['image/jpeg', 'image/gif', 'image/png']);
    }

}
