<?php

class CResources_Engine_Pdf extends CResources_Engine {
    public function __construct($type, $org_code) {
        parent::__construct('Pdf', $type, $org_code);
    }

    public static function factory($resource_type, $type, $org_code = null) {
        return new CResources_Engine_Pdf($resource_type, $type, $org_code);
    }

    public function save($file_name, $file_request) {
        $filename = parent::save($file_name, $file_request);
        $fullfilename = parent::getPath($filename);
        $path = dirname($fullfilename) . DS;
        return $filename;
    }
}
