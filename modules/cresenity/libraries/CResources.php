<?php

class CResources {

    use CTrait_Compat_Resources;

    public static function getFileInfo($filename) {
        $orgCode = '';
        $resource_type = '';
        $type = '';
        $date = '';
        $arr_name = explode("_", $filename);
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
        if ($orgCode == 'default')
            $orgCode = null;
        return array(
            'org_code' => $orgCode,
            'resource_type' => $resource_type,
            'type' => $type,
            'date' => $date,
        );
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
        //try to get file_info
        $filepath = CResources::get_path($resource_type);
        if (file_exists($filepath)) {
            $info = CResources::get_file_info($resource_type);
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
     * @param string $name
     * @param array $options
     * @return \CResources_Loader_Image
     */
    public static function image($name, $options = array()) {
        return new CResources_Loader_Image($name, $options);
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
    public static function get_directory($org_code, $app_code) {
        $root_directory = DOCROOT . 'application' . DS . $app_code . DS . 'default' . DS . 'resources' . DS . $org_code;
        $dir = $root_directory;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $data = array();
        $files = scandir($dir);
        $content = array_values(array_diff($files, array('.', '..')));
        if (count($content) > 0) {
            foreach ($content as $file) {
                $data[] = $file;
            }
        }
        
        return $data;
    }
    
}
