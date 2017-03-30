<?php

    require_once dirname(__FILE__) . DS . 'CResourcesEncode' . EXT;

    class CResourcesEngine {

        protected $_resource_type;
        protected $_type;
        protected $_org_code;
        protected $_sizes;
        protected $_filename;
        protected $_root_directory;

        protected function __construct($resource_type, $type, $org_code = null) {
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
            }
            if ($size != null) {
                $temp.=$size . DS;
            }
            $temp.=$filename;
            $dir = CF::get_dir($this->_root_directory);
            $temp_path = str_replace(DS, "/", $dir) . "" . $temp;
            
            return $temp_path;
        }

        public function save($file_name, $file_request) {
            $date_now = date("Y-m-d H:i:s");
            $dir = CF::get_dir($this->_root_directory);

            $org_code = $this->_org_code;
            if ($org_code == null) $org_code = 'default';
            $dir .= $org_code . DS;
            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $dir .= $this->_resource_type . DS;
            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $dir .= $this->_type . DS;
            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $dir .= date('YmdHis', strtotime($date_now)) . DS;
            if (!is_dir($dir)) {
                mkdir($dir);
            }


            $temp_file_name = $org_code . '_' . $this->_resource_type . "_" . $this->_type . "_" . date('YmdHis', strtotime($date_now)) . "_" . $file_name;
            $path = $dir . $temp_file_name;
            file_put_contents($path, $file_request);
            $this->_filename = $temp_file_name;
            return $temp_file_name;
        }

        public function get_url($filename = null,$size='') {
            if ($filename == null) $filename = $this->_filename;
            $size_add = $size;
            if(strlen($size_add)>0) {
                $size_add .= '/';
            }
            $path = curl::base(false, 'http') . 'assets/image/' . $size_add . CResourcesEncode::encode($filename);
//            $file_name_encode = $this->encode($file_name,self::_digit);
            return $path;
        }

        public function add_size($size_name, $options) {
            $this->_sizes[$size_name] = $options;
        }

        public function get_root_directory() {
            return $this->_root_directory;
        }

        public function set_root_directory($_root_directory) {
            $this->_root_directory = $_root_directory;
            return $this;
        }

    }
    