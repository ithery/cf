<?php

interface CResources_ImageGenerator_FileTypeInterface {
    public function canConvert(CApp_Model_Interface_ResourceInterface $resource);

    /**
     * Receive a file and return a thumbnail in jpg/png format.
     *
     * @param string                     $path
     * @param CResources_Conversion|null $conversion
     *
     * @return string
     */
    public function convert($path, CResources_Conversion $conversion = null);

    /**
     * @param string $mime
     *
     * @return bool
     */
    public function canHandleMime($mime = '');

    /**
     * @param string $extension
     *
     * @return bool
     */
    public function canHandleExtension($extension = '');

    /**
     * @return string
     */
    public function getType();
}
