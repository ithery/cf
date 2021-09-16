<?php

class CResources_Exception_FileCannotBeAdded_MimeTypeNotAllowed extends CResources_Exception_FileCannotBeAdded {
    public static function create(string $file, array $allowedMimeTypes) {
        $mimeType = mime_content_type($file);

        $allowedMimeTypes = implode(', ', $allowedMimeTypes);

        return new static("File has a mime type of {$mimeType}, while only {$allowedMimeTypes} are allowed");
    }
}
