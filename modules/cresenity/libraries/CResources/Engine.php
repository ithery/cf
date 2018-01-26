<?php

abstract class CResources_Engine implements CResources_EngineInterface {

    protected $_resource_type;
    protected $_type;
    protected $_org_code;
    protected $_sizes;
    protected $_filename;
    protected $_root_directory;

    public function __construct($resource_type, $type, $org_code = null) {
        $this->_resource_type = strtolower($resource_type);
        $this->_type = $type;
        $this->_org_code = $org_code;
        $this->_sizes = array();
        $this->_root_directory = 'resources';
    }

    public function get_path($filename, $size = null) {
        $temp = '';
        $arr_name = explode("_", $filename);
        if (count($arr_name) > 1) {
            //org_code
            if (isset($arr_name[0])) {
                $temp .= $arr_name[0] . DS;
            }
            //resource_type
            if (isset($arr_name[1])) {
                $temp .= $arr_name[1] . DS;
            }
            //name
            if (isset($arr_name[2])) {
                $temp .= $arr_name[2] . DS;
            }
            //date
            if (isset($arr_name[3])) {
                $temp .= $arr_name[3] . DS;
            }
        }
        if ($size != null) {
            $temp .= $size . DS;
        }
        $temp .= $filename;
        $dir = $this->_root_directory . DS;
        $temp_path = str_replace(DS, "/", $dir) . "" . $temp;

        return $temp_path;
    }

    public static function decode($filename) {
        return CResources_Decode::decode($filename);
    }

    public static function encode($filename) {
        return CResources_Encode::encode($filename);
    }

    public function save($file_name, $file_request) {
        $date_now = date("Y-m-d H:i:s");

        $dir = $this->_root_directory . DS;

        $org_code = $this->_org_code;
        if ($org_code == null)
            $org_code = 'default';
        $dir .= $org_code . DS;


        $dir .= $this->_resource_type . DS;


        $dir .= $this->_type . DS;


        $dir .= date('YmdHis', strtotime($date_now)) . DS;

        cfs::mkdir($dir);

        $temp_file_name = $org_code . '_' . $this->_resource_type . "_" . $this->_type . "_" . date('YmdHis', strtotime($date_now)) . "_" . $file_name;
        $path = $dir . $temp_file_name;
        $written = cfs::atomic_write($path, $file_request);

        if ($written === false) {
            throw new CResources_Exception(sprintf('The %s resource file is not writable.', $path));
        }
        $this->_filename = $temp_file_name;
        return $temp_file_name;
    }

    public function get_url($filename = null, $size = '') {



        if ($filename == null) {
            $filename = $this->_filename;
        }
        if ($this->_resource_type == 'image') {
            $imageLoader = CResources::image($filename);
            if (strlen($size) > 0) {
                $imageLoader->setSize($size);
            }
            return $imageLoader->getUrl();
        }

        $size_add = $size;
        if (strlen($size_add) > 0) {
            $size_add .= '/';
        }
        $http_or_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $path = curl::base(false, $http_or_https) . 'assets/image/' . $size_add . CResources_Encode::encode($filename);
//            $file_name_encode = $this->encode($file_name,self::_digit);
        return $path;
    }

    public function get_root_directory() {
        return $this->_root_directory;
    }

    public function set_root_directory($_root_directory) {
        $this->_root_directory = $_root_directory;
        return $this;
    }

}
