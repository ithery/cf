<?php

/**
 * Description of Image
 *
 * @author Hery
 */
class CResources_Loader_Image {

    protected $appCode = '';
    protected $orgCode = '';
    protected $resourceName = '';
    protected $resourceType = 'image';
    protected $sizeName = 'original';
    protected $type = '';
    protected $s3Options = null;
    protected $s3Object = null;

    public function __construct($resourceName, $options = array()) {

        $appCode = carr::get($options, 'app_code');
        $orgCode = carr::get($options, 'org_code');
        $sizeName = carr::get($options, 'size');
        $sizeOptions = carr::get($options, 'size_options', array());
        $type = carr::get($options, 'type');
        if (strlen($appCode) == 0) {
            $appCode = CF::app_code();
        }
        $s3Options = carr::get($options, 's3_options');


        //try to get info from resourceName
        $this->appCode = $appCode;
        $this->orgCode = $orgCode;
        $this->resourceName = $resourceName;

        if ($sizeName != null) {
            $this->setSize($sizeName, $sizeOptions);
        }
        $this->sizeName = $sizeName;
        $this->type = $type;
        $this->s3Options = $s3Options;


        $bucket = carr::get($this->s3Options, 'bucket');
        if (strlen($bucket) > 0) {
            $this->s3Object = DigitalOcean_Client::factory();
            $this->s3Object->setBucket($bucket);
        }
    }

    protected function getBasePath($sizeName = null) {
        $filename = $this->resourceName;
        $temp = '';
        $arrName = explode("_", $this->resourceName);
        //org_code
        if (isset($arrName[0])) {
            $temp .= $arrName[0] . DS;
        }
        //resource_type
        if (isset($arrName[1])) {
            $temp .= $arrName[1] . DS;
        }
        //name
        if (isset($arrName[2])) {
            $temp .= $arrName[2] . DS;
        }
        //date
        if (isset($arrName[3])) {
            $temp .= $arrName[3] . DS;
        }
        if ($sizeName != null) {
            $temp .= $sizeName . DS;
        }
        $temp .= $filename;
        $dir = '/application/' . $this->appCode . '/' . (strlen($this->orgCode) > 0 ? $this->orgCode : 'default') . '/resources/';
        $basepath = $dir . $temp;
        return $basepath;
    }

    public function getSizePath($sizeName = null) {
        $basePath = $this->getBasePath($sizeName);
        $fullPath = DOCROOT . ltrim($basePath, '/');

        return $fullPath;
    }

    public function getPath() {
        return $this->getSizePath($this->sizeName);
    }

    public function setSize($sizeName, $sizeOptions = array()) {
        $sizeDefaultWidth = 100;
        $sizeDefaultHeight = 100;
        if (count(explode("x", $sizeName)) == 2) {
            $sizeNameArray = explode("x", $sizeName);
            if (is_numeric($sizeNameArray[0]) && is_numeric($sizeNameArray[1])) {
                $sizeDefaultWidth = $sizeNameArray[0];
                $sizeDefaultHeight = $sizeNameArray[1];
            }
        }
        $width = carr::get($sizeOptions, 'width', $sizeDefaultWidth);
        $height = carr::get($sizeOptions, 'height', $sizeDefaultHeight);
        $crop = carr::get($sizeOptions, 'crop', true);
        $proportional = carr::get($sizeOptions, 'proportional', false);
        $whitespace = carr::get($sizeOptions, 'whitespace', false);

        $resizedPath = $this->getSizePath($sizeName);
        if (!file_exists($resizedPath)) {
            //we create new file for this size
            $originalPath = $this->getSizePath(null);
            $engine = new CResources_Engine_Image($this->type, $this->orgCode);
            $engine->resizeAndSave($originalPath, $sizeName, array(
                'width' => $width,
                'height' => $height,
                'crop' => $crop,
                'proportional' => $proportional,
                'whitespace' => $whitespace,
            ));
        }
        $this->sizeName = $sizeName;
        return $this;
    }

    public function getUrl($encoded = false) {
        $size_add = $this->sizeName;
        if (strlen($size_add) > 0) {
            $size_add .= '/';
        }
        
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
    
        $baseUrl = curl::base(false, $protocol);
        
        $path = $baseUrl . 'assets/image/' . $size_add . CResources_Encode::encode($this->resourceName);
        if ($this->s3Object != null) {
            $this->saveToS3();
            $baseUrl = trim($this->s3Object->getBucketEndPoint(), '/') . '/';
        }

        if (!$encoded) {
            $path = $baseUrl . 'application/' . $this->appCode . '/' . (strlen($this->orgCode) > 0 ? $this->orgCode : 'default') . '/resources/';
            $temp = '';
            $arr_name = explode("_", $this->resourceName);
            //org_code
            if (isset($arr_name[0])) {
                $temp .= $arr_name[0] . '/';
            }
            //resource_type
            if (isset($arr_name[1])) {
                $temp .= $arr_name[1] . '/';
            }
            //name
            if (isset($arr_name[2])) {
                $temp .= $arr_name[2] . '/';
            }
            //date
            if (isset($arr_name[3])) {
                $temp .= $arr_name[3] . '/';
            }
            if ($this->sizeName != null) {
                $temp .= $this->sizeName . '/';
            }
            $temp .= $this->resourceName;
            $path .= $temp;
        }
        return $path;
    }

    public function saveToS3() {
        $resultSave = false;
        if ($this->s3Object != null) {
            $this->s3Object->setBucket($bucket);

            $fullPath = $this->getSizePath($this->sizeName);
            $basePath = $this->getBasePath($this->sizeName);
            $path = trim(trim(dirname($basePath), '/'), DS);

            if (file_exists($fullPath)) {
                $resultSave = $s3Object->upload($path, file_get_contents($fullPath), $this->resourceName);
                if ($resultSave) {
                    //@unlink($fullPath);
                }
            }
        }

        return $resultSave;
    }

    public function delete() {
        $fullPath = $this->getSizePath($this->sizeName);
        
        unlink($fullPath);
    }
    
}
