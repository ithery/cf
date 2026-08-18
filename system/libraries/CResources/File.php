<?php

defined('SYSPATH') or die('No direct access allowed.');

class CResources_File {
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $size;

    /**
     * @var string
     */
    public $mimeType;

    public static function createFromResource($resource) {
        return new static($resource->file_name, $resource->size, $resource->mime_type);
    }

    public function __construct($name, $size, $mimeType) {
        $this->name = $name;
        $this->size = $size;
        $this->mimeType = $mimeType;
    }

    #[\ReturnTypeWillChange]
    public function __toString() {
        return "name: {$this->name}, size: {$this->size}, mime: {$this->mimeType}";
    }
}
