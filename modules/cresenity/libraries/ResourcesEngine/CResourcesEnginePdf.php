<?php

require_once dirname(__FILE__) . DS . CResources::_prefix . EXT;

class CResourcesEnginePdf extends CResourcesEngine {

    protected function __construct($resource_type, $type, $org_code) {
        parent::__construct($resource_type, $type, $org_code);
    }

    public static function factory($resource_type, $type, $org_code = null) {
        return new CResourcesEnginePdf($resource_type, $type, $org_code);
    }

    public function save($file_name, $file_request) {
        $filename = parent::save($file_name, $file_request);
        $fullfilename = parent::get_path($filename);
        $path = dirname($fullfilename) . DS;
        return $filename;
    }

    public function get_url($filename = null) {
        if ($filename == null)
            $filename = $this->_filename;

        $path = curl::base(false, 'http') . 'assets/pdf/' . CResourcesEncode::encode($filename);
        return $path;
    }

}
