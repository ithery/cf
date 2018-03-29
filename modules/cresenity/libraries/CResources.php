<?php

class CResources {

    public static function get_file_info($filename) {
        $org_code = '';
        $resource_type = '';
        $type = '';
        $date = '';
        $arr_name = explode("_", $filename);
        //org_code
        if (isset($arr_name[0])) {
            $org_code = $arr_name[0];
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
        if ($org_code == 'default')
            $org_code = null;
        return array(
            'org_code' => $org_code,
            'resource_type' => $resource_type,
            'type' => $type,
            'date' => $date,
        );
    }

    public static function get_path($filename, $size = null) {
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
    public static function factory($resource_type, $type, $org_code = null) {
        $root_directory = DOCROOT . 'application' . DS . CF::app_code() . DS . 'default' . DS . 'resources';
        //try to get file_info
        $filepath = CResources::get_path($resource_type);
        if (file_exists($filepath)) {
            $info = CResources::get_file_info($resource_type);
            $resource_type = carr::get($info, 'resource_type');
            $type = carr::get($info, 'type');
            $org_code = carr::get($info, 'org_code');
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
        $object = new $class($type, $org_code);
        $object->set_root_directory($root_directory);
        return $object;
    }

    public static function image($name, $options = array()) {
        return new CResources_Loader_Image($name, $options);
    }

    public static function encode($str) {
        return CResources_Encode::encode($str);
    }

    public static function decode($str) {
        return CResources_Decode::decode($str);
    }

}
