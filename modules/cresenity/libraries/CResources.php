<?php

class CResources {

    use CTrait_Compat_Resources;

    /**
     * 
     * @return CStorage_Adapter
     */
    public static function disk($diskName = null) {
        if ($diskName == null) {
            $diskName = CF::config('resource.disk');
        }

        return CStorage::instance()->disk($diskName);
    }

    public static function isS3($diskName=null) {
         if($diskName==null) {
            $diskName = CF::config('resource.disk');
        }
       
        $config = CF::config("storage.disks.{$diskName}");
        return carr::get($config,'driver') == 's3';
    }
    

    public static function isOldSystem() {
        return CF::config('resource.disk') == null;
    }

    public static function getFileInfo($filename) {
        $orgCode = '';
        $resource_type = '';
        $type = '';
        $date = '';
        $arr_name = explode("_", $filename);
        $count_arr_name = count($arr_name);
        //org_code
        if (isset($arr_name[0])) {
            $orgCode = $arr_name[0];
        }
        //resource_type
        if (isset($arr_name[1])) {
            $resource_type = $arr_name[1];
        }
        //type
        if (isset($arr_name[2])) {
            $type = $arr_name[2];
        }
        //date
        if (isset($arr_name[3])) {
            $date = $arr_name[3];
        }
        //name
        if (isset($arr_name[4])) {
            $name = $arr_name[4];
            $file_name = array();
            $i = 4;
            if ($count_arr_name > $i) {
                for ($i; $i < $count_arr_name; $i++) {
                    $file_name[$i] = $arr_name[$i];
                }
                $name = implode("_", $file_name);
            }
        }
        if ($orgCode == 'default')
            $orgCode = null;
        return array(
            'org_code' => $orgCode,
            'resource_type' => $resource_type,
            'type' => $type,
            'date' => $date,
            'name' => $name,
        );
    }

    public static function exists($filename, $size = null) {
        $path = static::getPath($filename, $size);
        return file_exists($path);
    }

    public static function getRelativePath($filename, $size = null) {

        $temp = '';
        $arr_name = explode("_", $filename);
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
        if ($size != null) {
            $temp .= $size . DS;
        }
        $temp .= $filename;
        $dir = 'resources';

        $temp_path = str_replace(DS, "/", $dir) . "" . $temp;
        return $temp_path;
    }

    public static function getPath($filename, $size = null) {

        $temp = '';
        $arr_name = explode("_", $filename);
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
        if ($size != null) {
            $temp .= $size . DS;
        }
        $temp .= $filename;
        $dir = CF::get_dir('resources');

        $temp_path = str_replace(DS, "/", $dir) . "" . $temp;
        return $temp_path;
    }

    /**
     * Currently just support image
     * 
     * @param string $resource_type Possible value: image, pdf, dll or filename if 
     * @param string $type              
     * @param string $type              
     * 
     * @return CResources_Engine
     */
    public static function factory($resource_type, $type, $options = array()) {
        $appCode = CF::appCode();
        $orgCode = $options;

        if (is_array($options)) {
            $orgCode = carr::get($options, 'org_code');
            $appCode = carr::get($options, 'app_code');
        } else {
            CCollector::deprecated('Resources options must passed as array');
        }
        if (!is_array($orgCode)) {
            if (strlen($orgCode) == 0) {
                $orgCode = CF::orgCode();
            }
        }
        if (strlen($appCode) == 0) {
            $appCode = CF::appCode();
        }

        if (!is_array($options)) {
            $options = array(
                'org_code' => $orgCode,
                'app_code' => $appCode,
            );
        }
        $root_directory = DOCROOT . 'application' . DS . $appCode . DS . 'default' . DS . 'resources';

//        if(CResources::isS3()) {
//             $root_directory = 'resources';
//        }
        //try to get file_info
        $filepath = CResources::getPath($resource_type);
        if (file_exists($filepath)) {
            $info = CResources::getFileInfo($resource_type);
            $resource_type = carr::get($info, 'resource_type');
            $type = carr::get($info, 'type');
            $orgCode = carr::get($info, 'org_code');
        }

        //validate resource_type and type
        if (strpos($resource_type, '_') !== false) {
            throw new CResources_Exception('Resource type cannot have underscore character');
        }
        if (strpos($type, '_') !== false) {
            throw new CResources_Exception('Resource type cannot have underscore character');
        }

        $chr = mb_substr($resource_type, 0, 1, "UTF-8");
        if (mb_strtolower($chr, "UTF-8") == $chr) {
            $resource_type = ucfirst($resource_type);
        }

        $class = 'CResources_Engine_' . $resource_type;
        $object = new $class($type, $options);
        $object->set_root_directory($root_directory);
        return $object;
    }

    /**
     * 
     * @param string $type
     * @param array $options
     * @return CResources_Engine_Image
     */
    public static function imageEngine($type = 'Image', $options = array()) {
        return self::factory('Image', $type, $options);
    }

    /**
     * 
     * @param string $name
     * @param array $options
     * @return \CResources_Loader_Image
     */
    public static function image($name, $options = array()) {
        return new CResources_Loader_Image($name, $options);
    }

    /**
     * 
     * @param type $name
     * @param type $options
     * @return \CResources_Loader_File
     * @deprecated since version 1.1
     */
    public static function files($name, $options = array()) {
        return self::file($name, $options);
    }

    /**
     * 
     * @param type $name
     * @param type $options
     * @return \CResources_Loader_File
     */
    public static function file($name, $options = array()) {
        return new CResources_Loader_File($name, $options);
    }

    /**
     * 
     * @param string $str
     * @return string
     */
    public static function encode($str) {
        return CResources_Encode::encode($str);
    }

    /**
     * 
     * @param string $str
     * @return string
     */
    public static function decode($str) {
        return CResources_Decode::decode($str);
    }

    /**
     * 
     * @param type $org_code
     * @param type $app_code
     */
    public static function get_all_file($org_code, $app_code, $resource_type, $depth = 0) {
        $root_directory = DOCROOT . 'application' . DS . $app_code . DS . 'default' . DS . 'resources' . DS . $org_code . DS . $resource_type;
        $files = CResources::scanDirectory($root_directory);
        return $files;
    }

    public static function scanDirectory($dir, $filter = "", &$results = array()) {
        $scan = scandir($dir);

        foreach ($scan as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

            if (!is_dir($path)) {
                if (empty($filter) || preg_match($filter, $path)) {
                    $results[] = basename($path);
                }
            } elseif ($value != "." && $value != "..") {
                CResources::scanDirectory($path, $filter, $results);
            }
        }
        return $results;
    }

    public static function saveFromTemp($type, $resourceName, $tempPath, $resourceOptions = array()) {
        if (!is_array($resourceOptions)) {
            $resourceOptions = array();
        }

        if (!isset($resourceOptions['app_code'])) {
            $resourceOptions['app_code'] = CF::appCode();
        }

        $resource = CResources::factory($type, $resourceName, $resourceOptions);
        $filename = basename($tempPath);
        $imageName = $resource->saveFromTemp($filename, $tempPath);
        return $imageName;
    }

    public static function getUrl($fileId) {
        $path = static::getPath($fileId);
        $url = str_replace(DOCROOT, curl::httpbase(), $path);
        return $url;
    }

    public static function delete($filename, $size = null) {
        if (static::exists($filename, $size)) {
            $path = static::getPath($filename);
            return unlink($path);
        }
        return true;
    }

}
