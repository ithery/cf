<?php

namespace League\MimeTypeDetection;

interface ExtensionToMimeTypeMap {
    /**
     * @param string $extension
     *
     * @return null|string
     */
    public function lookupMimeType($extension);
}
