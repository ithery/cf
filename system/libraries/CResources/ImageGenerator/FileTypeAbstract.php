<?php

abstract class CResources_ImageGenerator_FileTypeAbstract implements CResources_ImageGenerator_FileTypeInterface {
    public function canConvert(CModel_Resource_ResourceInterface $resource) {
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
    abstract public function requirementsAreInstalled() : bool;

    /**
     * @return CCollection
     */
    abstract public function supportedExtensions() : CCollection;

    /**
     * @return CCollection
     */
    abstract public function supportedMimetypes() : CCollection;
}
