<?php

abstract class CResources_FileNamerAbstract {
    /**
     * @param string $fileName
     *
     * @return string
     */
    public function originalFileName($fileName) {
        $extLength = strlen(pathinfo($fileName, PATHINFO_EXTENSION));

        $baseName = substr($fileName, 0, strlen($fileName) - ($extLength ? $extLength + 1 : 0));

        return $baseName;
    }

    /**
     * @param string                $fileName
     * @param CResources_Conversion $conversion
     *
     * @return string
     */
    abstract public function conversionFileName($fileName, CResources_Conversion $conversion);

    /**
     * @param string $fileName
     *
     * @return string
     */
    abstract public function responsiveFileName($fileName);

    /**
     * @param CModel_Resource_ResourceInterface $resource
     * @param string                            $extension
     *
     * @return string
     */
    public function temporaryFileName(CModel_Resource_ResourceInterface $resource, $extension) {
        return "{$this->responsiveFileName($resource->file_name)}.{$extension}";
    }

    /**
     * @param string $baseImage
     *
     * @return string
     */
    public function extensionFromBaseImage($baseImage) {
        return pathinfo($baseImage, PATHINFO_EXTENSION);
    }
}
