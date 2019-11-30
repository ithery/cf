<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

interface CResources_ImageGenerator_FileTypeInterface {

    public function canConvert(CApp_Model_Interface_ResourceInterface $resource);

    /**
     * Receive a file and return a thumbnail in jpg/png format.
     *
     * @param string $path
     * @param \Spatie\MediaLibrary\Conversion\Conversion|null $conversion
     *
     * @return string
     */
    public function convert($path, Conversion $conversion = null);

    /**
     * 
     * @param string $mime
     * @return bool
     */
    public function canHandleMime($mime = '');

    /**
     * 
     * @param string $extension
     * @return bool
     */
    public function canHandleExtension($extension = '');

    /**
     * @return string
     */
    public function getType(): string;
}
