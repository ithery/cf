<?php

use Symfony\Component\Mime\MimeTypes;

class CHTTP_Testing_MimeType {
    /**
     * The mime types instance.
     *
     * @var null|\Symfony\Component\Mime\MimeTypes
     */
    private static $mime;

    /**
     * Get the mime types instance.
     *
     * @return \Symfony\Component\Mime\MimeTypesInterface
     */
    public static function getMimeTypes() {
        if (self::$mime === null) {
            self::$mime = new MimeTypes();
        }

        return self::$mime;
    }

    /**
     * Get the MIME type for a file based on the file's extension.
     *
     * @param string $filename
     *
     * @return string
     */
    public static function from($filename) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return self::get($extension);
    }

    /**
     * Get the MIME type for a given extension or return all mimes.
     *
     * @param string $extension
     *
     * @return string
     */
    public static function get($extension) {
        return carr::first(self::getMimeTypes()->getMimeTypes($extension)) ?? 'application/octet-stream';
    }

    /**
     * Search for the extension of a given MIME type.
     *
     * @param string $mimeType
     *
     * @return null|string
     */
    public static function search($mimeType) {
        return carr::first(self::getMimeTypes()->getExtensions($mimeType));
    }
}
