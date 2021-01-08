<?php

class CResources_Engine_File extends CResources_Engine {
    public function __construct($type, $options) {
        parent::__construct('File', $type, $options);
    }

    public function save($file_name, $file_request) {
        $filename = parent::save($file_name, $file_request);
        $fullfilename = parent::get_path($filename);
        $path = dirname($fullfilename) . DS;

        return $filename;
    }
}
