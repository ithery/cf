<?php

abstract class CResources_ImageGenerator_FileTypeAbstract implements CResources_ImageGenerator_FileTypeInterface {
    public function canConvert(CApp_Model_Interface_ResourceInterface $resource) {
        if (!$this->requirementsAreInstalled()) {
            return false;
        }
        if ($this->supportedExtensions()->contains(strtolower($resource->extension))) {
            return true;
        }
        if ($this->supportedMimetypes()->contains(strtolower($resource->mime_type))) {
            return true;
        }
        return false;
    }

    public function canHandleMime($mime = '') {
        return $this->supportedMimetypes()->contains($mime);
    }

    public function canHandleExtension($extension = '') {
        return $this->supportedExtensions()->contains($extension);
    }

    public function getType() {
        return strtolower(c::classBasename(static::class));
    }

    /**
     * @return bool
     */
    abstract public function requirementsAreInstalled();

    /**
     * @return CCollection
     */
    abstract public function supportedExtensions();

    /**
     * @return CCollection
     */
    abstract public function supportedMimetypes();
}
