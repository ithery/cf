<?php

    require_once dirname(__FILE__) . DS . 'ResourcesEngine' . DS . 'CResourcesEncode' . EXT;
    require_once dirname(__FILE__) . DS . 'ResourcesEngine' . DS . 'CResourcesDecode' . EXT;

    class CResources {

        protected $_engine;
        protected $_resource_type;
        protected $_type;
        protected $_org_code;
        protected $_root_directory;

        public static $root_directory;
        const _prefix = "CResourcesEngine";

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
            if ($org_code == 'default') $org_code = null;
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
            if ($size != null) {
                $temp.=$size . DS;
            }
            $temp.=$filename;
            $dir = CF::get_dir('resources');
            
            $temp_path = str_replace(DS, "/", $dir) . "" . $temp;
            return $temp_path;
        }

        private function __construct($resource_type, $type = null, $org_code = null) {

            $this->_root_directory = 'resources';
            
            //try to get file_info
            $filepath = CResources::get_path($resource_type);
            if (file_exists($filepath)) {
                $info = CResources::get_file_info($resource_type);
                $this->_resource_type = carr::get($info, 'resource_type');
                $this->_type = carr::get($info, 'type');
                $this->_org_code = carr::get($info, 'org_code');
            }
            else {
                $this->_resource_type = $resource_type;
                $this->_type = $type;
                $this->_org_code = $org_code;
            }

            //try to locate the resource if filename is given
            


            $file_name = self::_prefix . ucfirst(strtolower($resource_type));
            $path = dirname(__FILE__) . DS . "ResourcesEngine" . DS . $file_name . EXT;
            require_once $path;
            $this->_engine = $file_name::factory($resource_type, $type, $org_code);
        }

        /**
         * Currently just support image
         * 
         * @param String $resource_type     Possible value: image, pdf, dll
         * @param String $type              
         * @return \CResources
         */
        public static function factory($resource_type, $type, $org_code = null) {
            return new CResources($resource_type, $type, $org_code);
        }

        public function save($file_name, $file_request) {
            $this->_engine->set_root_directory($this->_root_directory);
            return $this->_engine->save($file_name, $file_request);
        }

        /**
         * This function is used for get URL Image to show the image.
         * 
         * @param String $file_name Filename included extensions
         * @return type
         */
        public function get_url($file_name,$size='') {
            return $this->_engine->get_url($file_name,$size);
        }

        public static function decode($filename) {
            return CResourcesDecode::decode($filename);
        }

        public static function encode($filename) {
            return CResourcesEncode::encode($filename);
        }

        public function add_size($size_name, $options) {
            if ($this->_resource_type != 'image') {
                throw new Exception('Resource Type Image not supported');
            }

            return $this->_engine->add_size($size_name, $options);
        }

        public function get_root_directory() {
            return $this->_root_directory;
        }

        public function set_root_directory($_root_directory) {
            $this->_root_directory = $_root_directory;
            return $this;
        }

    }
    