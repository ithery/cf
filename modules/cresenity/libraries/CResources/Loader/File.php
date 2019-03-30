<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CResources_Loader_File extends CResources_LoaderAbstract {

    protected $appCode = '';
    protected $orgCode = '';
    protected $resourceName = '';
    protected $resourceType = 'file';
    protected $type = '';

    public function __construct($resourceName, $options = array()) {
        $appCode = carr::get($options, 'app_code');
        $orgCode = carr::get($options, 'org_code');
        $type = carr::get($options, 'type');
        if (strlen($appCode) == 0) {
            $appCode = CF::app_code();
        }

        $this->appCode = $appCode;
        $this->orgCode = $orgCode;
        $this->resourceName = $resourceName;
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

        $temp .= $filename;
        $dir = DOCROOT.'/application/' . $this->appCode . '/' . (strlen($this->orgCode) > 0 ? $this->orgCode : 'default') . '/resources/';
        $basepath = $dir . $temp;
        return $basepath;
    }

    public function getUrl($encoded = false) {

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";

        $baseUrl = curl::base(false, $protocol);
        if (!$encoded) {
            $path = $baseUrl . 'application/' . $this->appCode . '/' . (strlen($this->orgCode) > 0 ? $this->orgCode : 'default') . '/resources/';
            $temp = '';
            $arr_name = explode("_", $this->resourceName);
            //org_code
            if (isset($arr_name[0])) {
                $temp .= rawurlencode($arr_name[0]) . '/';
            }
            //resource_type
            if (isset($arr_name[1])) {
                $temp .= rawurlencode($arr_name[1]) . '/';
            }
            //name
            if (isset($arr_name[2])) {
                $temp .= rawurlencode($arr_name[2]) . '/';
            }
            //date
            if (isset($arr_name[3])) {
                $temp .= rawurlencode($arr_name[3]) . '/';
            }
            $temp .= rawurlencode($this->resourceName);
            $path .= $temp;
        }
        return $path;
    }

    public function getPath() {
        return $this->getBasePath();
    }

    public function rename($oldFile, $newFile) {
        $old = CResources::getPath($oldFile);
        $new = CResources::getPath($newFile);
        if (rename($old, $new)) {
            return true;
        } else {
            return false;
        }
    }

}
