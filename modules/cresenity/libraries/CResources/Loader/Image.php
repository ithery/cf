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

    public function __construct($resourceName, $options = array()) {

        $appCode = carr::get($options, 'app_code');
        $orgCode = carr::get($options, 'org_code');
        $sizeName = carr::get($options, 'size');
        $type = carr::get($options, 'type');
        if (strlen($appCode) == 0) {
            $appCode = CF::app_code();
        }
        //try to get info from resourceName
        $this->appCode = $appCode;
        $this->orgCode = $orgCode;
        $this->resourceName = $resourceName;
        $this->sizeName = $sizeName;
        $this->type = $type;
    }

    public function getSizePath($sizeName = null) {

        $filename = $this->resourceName;
        $temp = '';
        $arrName = explode("_", $this->resourceName);
        //org_code
        if (isset($arrName[0])) {
            $temp.=$arrName[0] . DS;
        }
        //resource_type
        if (isset($arrName[1])) {
            $temp.=$arrName[1] . DS;
        }
        //name
        if (isset($arrName[2])) {
            $temp.=$arrName[2] . DS;
        }
        //date
        if (isset($arrName[3])) {
            $temp.=$arrName[3] . DS;
        }
        if ($sizeName != null) {
            $temp.=$sizeName . DS;
        }
        $temp.=$filename;
        $dir = DOCROOT . 'application/' . $this->appCode . '/' . (strlen($this->orgCode) > 0 ? $this->orgCode : 'default') . '/resources/';
        $temp_path = str_replace(DS, "/", $dir) . "" . $temp;
        return $temp_path;
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
        $path = curl::base(false, 'http') . 'assets/image/' . $size_add . CResources_Encode::encode($this->resourceName);
        if (!$encoded) {
            $path = curl::base(false, 'http') . 'application/' . $this->appCode . '/' . (strlen($this->orgCode) > 0 ? $this->orgCode : 'default') . '/resources/';
            $temp = '';
            $arr_name = explode("_", $this->resourceName);
            //org_code
            if (isset($arr_name[0])) {
                $temp.=$arr_name[0] . DS;
            }
            //resource_type
            if (isset($arr_name[1])) {
                $temp.=$arr_name[1] . DS;
            }
            //name
            if (isset($arr_name[2])) {
                $temp.=$arr_name[2] . DS;
            }
            //date
            if (isset($arr_name[3])) {
                $temp.=$arr_name[3] . DS;
            }
            if ($this->sizeName != null) {
                $temp.=$this->sizeName . DS;
            }
            $temp.=$this->resourceName;
            $path.=$temp;
        }
        return $path;
    }

}
