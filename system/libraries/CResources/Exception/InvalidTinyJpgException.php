<?php
class CResources_Exception_InvalidTinyJpgException extends CResources_Exception {
    /**
     * @param string $tinyImageDestinationPath
     *
     * @return self
     */
    public static function doesNotExist($tinyImageDestinationPath) {
        return new static("The expected tiny jpg at `{$tinyImageDestinationPath}` does not exist");
    }

    /**
     * @param string $tinyImageDestinationPath
     *
     * @return self
     */
    public static function hasWrongMimeType($tinyImageDestinationPath) {
        $foundMimeType = CResources_Helpers_File::getMimeType($tinyImageDestinationPath);

        return new static("Expected the file at {$tinyImageDestinationPath} have mimetype `image/jpeg`, but found a file with mimetype `{$foundMimeType}`");
    }
}
