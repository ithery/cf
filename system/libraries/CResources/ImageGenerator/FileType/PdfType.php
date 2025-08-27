<?php

class CResources_ImageGenerator_FileType_PdfType extends CResources_ImageGenerator_FileTypeAbstract {
    /**
     * @param string                $file
     * @param CResources_Conversion $conversion
     *
     * @return string
     */
    public function convert($file, CResources_Conversion $conversion = null) {
        $imageFile = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME) . '.jpg';
        $pageNumber = $conversion ? $conversion->getPdfPageNumber() : 1;

        $pdf = new CImage_Pdf($file);
        $pdf->selectPage($pageNumber)->save($imageFile);

        return $imageFile;
    }

    public function requirementsAreInstalled(): bool {
        if (!class_exists(Imagick::class)) {
            return false;
        }

        return true;
    }

    public function supportedExtensions(): CCollection {
        return c::collect(['pdf']);
    }

    public function supportedMimeTypes(): CCollection {
        return c::collect(['application/pdf']);
    }
}
