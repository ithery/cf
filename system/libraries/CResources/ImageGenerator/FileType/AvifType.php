<?php

class CResources_ImageGenerator_FileType_AvifType extends CResources_ImageGenerator_FileTypeAbstract {
    /**
     * @param string                $file
     * @param CResources_Conversion $conversion
     *
     * @return string
     */
    public function convert($file, CResources_Conversion $conversion = null) {
        $pathToImageFile = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME) . '.png';

        $image = imagecreatefromavif($file);

        imagepng($image, $pathToImageFile, 9);

        imagedestroy($image);

        return $pathToImageFile;
    }

    public function requirementsAreInstalled(): bool {
        if (!function_exists('imagecreatefromavif')) {
            return false;
        }

        if (!function_exists('imagepng')) {
            return false;
        }

        if (!function_exists('imagedestroy')) {
            return false;
        }

        return true;
    }

    public function supportedExtensions(): CCollection {
        return c::collect(['avif']);
    }

    public function supportedMimeTypes(): CCollection {
        return c::collect(['image/avif']);
    }
}
