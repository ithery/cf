<?php

abstract class CResources_Engine implements CResources_EngineInterface {
    use CTrait_Compat_Resources_Engine;

    protected $resourceType;

    protected $type;

    protected $orgCode;

    protected $appCode;

    protected $sizes;

    protected $filename;

    protected $rootDirectory;

    public function __construct($resourceType, $type, $options = []) {
        $this->resource_type = strtolower($resourceType);
        $this->type = $type;

        $this->orgCode = carr::get($options, 'org_code');
        $this->appCode = carr::get($options, 'app_code');
        $this->sizes = [];
        $this->rootDirectory = 'resources';
    }

    public function getPath($filename, $size = null) {
        $temp = '';
        $arr_name = explode('_', $filename);
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
        $dir = $this->rootDirectory . DS;
        $temp_path = str_replace(DS, '/', $dir) . '' . $temp;

        return $temp_path;
    }

    public static function decode($filename) {
        return CResources_Decode::decode($filename);
    }

    public static function encode($filename) {
        return CResources_Encode::encode($filename);
    }

    public function saveToDisk($file_name, $file_request, $disk = null) {
        if ($disk == null) {
            $disk = CResources::disk();
        }
        $date_now = date('Y-m-d H:i:s');

        $dir = $this->rootDirectory . DS;

        $org_code = $this->orgCode;
        if ($org_code == null) {
            $org_code = 'default';
        }
        $dir .= $org_code . DS;

        $dir .= $this->resource_type . DS;

        $dir .= $this->type . DS;

        $dir .= date('YmdHis', strtotime($date_now)) . DS;

        $temp_file_name = $org_code . '_' . $this->resource_type . '_' . $this->type . '_' . date('YmdHis', strtotime($date_now)) . '_' . $file_name;
        $path = $dir . $temp_file_name;

        if (cstr::startsWith($path, DOCROOT)) {
            $path = substr($path, strlen(DOCROOT));
        }

        $written = $disk->put($path, $file_request);

        if ($written === false) {
            throw new CResources_Exception(sprintf('The %s resource file is not writable.', $path));
        }
        $this->filename = $temp_file_name;

        return $temp_file_name;
    }

    public function save($file_name, $file_request) {
        $date_now = date('Y-m-d H:i:s');

        $dir = $this->rootDirectory . DS;

        $org_code = $this->orgCode;
        if ($org_code == null) {
            $org_code = 'default';
        }
        $dir .= $org_code . DS;

        $dir .= $this->resource_type . DS;

        $dir .= $this->type . DS;

        $dir .= date('YmdHis', strtotime($date_now)) . DS;

        $temp_file_name = $org_code . '_' . $this->resource_type . '_' . $this->type . '_' . date('YmdHis', strtotime($date_now)) . '_' . $file_name;
        $path = $dir . $temp_file_name;

        if (cstr::startsWith($path, DOCROOT)) {
            $path = substr($path, strlen(DOCROOT));
        }
        $disk = CResources::disk();
        $written = $disk->put($path, $file_request);

        if ($written === false) {
            throw new CResources_Exception(sprintf('The %s resource file is not writable.', $path));
        }
        $this->filename = $temp_file_name;

        return $temp_file_name;
    }

    public function saveFromTemporary($filename, $folder, $fileId) {
        $dateNow = date('Y-m-d H:i:s');

        $dir = $this->rootDirectory . DS;
        //$dir = '';
        $orgCode = $this->orgCode;
        if ($orgCode == null) {
            $orgCode = 'default';
        }
        $dir .= $orgCode . DS;

        $tempDisk = CStorage::instance()->temp();
        $tempPath = CTemporary::getPath($folder, $fileId);
        $dir .= $this->resource_type . DS;

        $dir .= $this->type . DS;

        $dir .= date('YmdHis', strtotime($dateNow)) . DS;

        //cfs::mkdir($dir);

        $tempFileName = $orgCode . '_' . $this->resource_type . '_' . $this->type . '_' . date('YmdHis', strtotime($dateNow)) . '_' . $filename;
        $path = $dir . $tempFileName;

        if (cstr::startsWith($path, DOCROOT)) {
            $path = substr($path, strlen(DOCROOT));
        }
        $resourceDisk = CResources::disk();
        $written = $resourceDisk->put($path, $tempDisk->get($tempPath));
        //$written = copy($tempPath, $path);

        if ($written === false) {
            throw new CResources_Exception(sprintf('The %s resource file is not writable.', $path));
        }
        $this->filename = $tempFileName;

        return $tempFileName;
    }

    public function saveFromTemp($file_name, $tempPath) {
        $date_now = date('Y-m-d H:i:s');

        $dir = $this->rootDirectory . DS;

        $org_code = $this->orgCode;
        if ($org_code == null) {
            $org_code = 'default';
        }
        $dir .= $org_code . DS;

        $dir .= $this->resource_type . DS;

        $dir .= $this->type . DS;

        $dir .= date('YmdHis', strtotime($date_now)) . DS;

        cfs::mkdir($dir);

        $temp_file_name = $org_code . '_' . $this->resource_type . '_' . $this->type . '_' . date('YmdHis', strtotime($date_now)) . '_' . $file_name;
        $path = $dir . $temp_file_name;
        $written = copy($tempPath, $path);

        if ($written === false) {
            throw new CResources_Exception(sprintf('The %s resource file is not writable.', $path));
        }
        $this->filename = $temp_file_name;

        return $temp_file_name;
    }

    public function saveFile($file_name, $file_request) {
        $date_now = date('Y-m-d H:i:s');

        $dir = $this->rootDirectory . DS;

        $org_code = $this->orgCode;
        if ($org_code == null) {
            $org_code = 'default';
        }
        $dir .= $org_code . DS;

        $dir .= $this->resource_type . DS;

        $dir .= $this->type . DS;

        $dir .= date('YmdHis', strtotime($date_now)) . DS;

        cfs::mkdir($dir);

        $temp_file_name = $org_code . '_' . $this->resource_type . '_' . $this->type . '_' . date('YmdHis', strtotime($date_now)) . '_' . $file_name;
        $path = $dir . $temp_file_name;

        $written = rename($file_request, $path);

        if ($written === false) {
            throw new CResources_Exception(sprintf('The %s resource file is not writable.', $path));
        }
        $this->filename = $temp_file_name;

        return $temp_file_name;
    }

    public function getUrl($filename = null, $size = '', $encode = true) {
        if ($filename == null) {
            $filename = $this->filename;
        }
        if ($this->resource_type == 'image') {
            $options = [
                'app_code' => $this->appCode,
            ];
            $imageLoader = CResources::image($filename, $options);
            if (strlen($size) > 0) {
                $imageLoader->setSize($size);
            }

            return $imageLoader->getUrl();
        }
        if ($this->resource_type == 'file' || $this->resource_type == 'pdf') {
            $options = [
                'app_code' => $this->appCode,
            ];
            $fileLoader = CResources::file($filename, $options);

            return $fileLoader->getUrl();
        }

        $size_add = $size;
        if (strlen($size_add) > 0) {
            $size_add .= '/';
        }
        $http_or_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $path = curl::base(false, $http_or_https) . 'assets/image/' . $size_add . CResources_Encode::encode($filename);
        if ($encode == false) {
            $path = curl::base(false, $http_or_https) . 'assets/image/' . $size_add . $filename;
        }

        return $path;
    }

    public function getRootDirectory() {
        return $this->rootDirectory;
    }

    public function setRootDirectory($_root_directory) {
        $this->rootDirectory = $_root_directory;

        return $this;
    }

    public function addSize($size_name, $options) {
        throw new Exception('not implemented');
    }
}
