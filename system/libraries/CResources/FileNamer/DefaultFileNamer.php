<?php

class CResources_FileNamer_DefaultFileNamer extends CResources_FileNamerAbstract {
    /**
     * @param string                $fileName
     * @param CResources_Conversion $conversion
     *
     * @return string
     */
    public function conversionFileName($fileName, CResources_Conversion $conversion) {
        $strippedFileName = pathinfo($fileName, PATHINFO_FILENAME);

        return "{$strippedFileName}-{$conversion->getName()}";
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function responsiveFileName($fileName) {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }
}
