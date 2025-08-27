<?php

class CResources_ImageGenerator_FileType_ImageType extends CResources_ImageGenerator_FileTypeAbstract {
    /**
     * @param string                $path
     * @param CResources_Conversion $conversion
     *
     * @return string
     */
    public function convert($path, CResources_Conversion $conversion = null) {
        return $path;
    }

    /**
     * @return bool
     */
    public function requirementsAreInstalled() : bool {
        return true;
    }

    /**
     * @return CCollection
     */
    public function supportedExtensions() : CCollection {
        return c::collect(['png', 'jpg', 'jpeg', 'gif']);
    }

    /**
     * @return CCollection
     */
    public function supportedMimeTypes() : CCollection {
        return c::collect(['image/jpeg', 'image/gif', 'image/png']);
    }
}
