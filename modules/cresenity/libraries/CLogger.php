<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Dec 3, 2014
     * @license http://piposystem.com Piposystem
     */
    class CLogger {

        const __EXT = ".log";
        // Log message levels - Windows users see PHP Bug #18090
        const EMERGENCY = 0;
        const ALERT = 1;
        const CRITICAL = 2;
        const ERROR = 3;
        const WARNING = 4;
        const NOTICE = 5;
        const INFO = 6;
        const DEBUG = 7;
        const TRACE = 8;

        private static $_instance = NULL;
        private $_log_threshold;
        private $_log_path = '';
        private $_file_path = '';
        private $_suffix_filename = 'log';
        private $_additional_path = NULL;
        private $_level = 6;
        private $_level_name;

        private function __construct() {
            

            $this->_log_threshold = CF::config('core.log_threshold');
            $log_threshold = ccfg::get('log_threshold');
            if ($log_threshold != NULL) $this->_log_threshold = $log_threshold;

            
        }

        private function __create_log_path() {
            $include_paths = CF::include_paths();
            $this->_log_path = $include_paths[0] . 'logs' . DS;
            foreach ($include_paths as $path) {
                if (is_dir($path)) {
                    $this->_log_path = $path . 'logs' . DS;
                    break;
                }
            }

            if (!file_exists($this->_log_path) && !is_dir($this->_log_path)) {
                mkdir($this->_log_path);
            }
            
            if ($this->_additional_path != NULL) {
                $temp_dir = explode(DS, $this->_additional_path);
                $temp_dir2 = explode('/', $this->_additional_path);
                if(count($temp_dir2)>1) {
                    $temp_dir = $temp_dir2;
                }
                foreach ($temp_dir as $k => $v) {
                    $this->_log_path .= $v .DS;
                    if (!file_exists($this->_log_path) && !is_dir($this->_log_path)) {
                        mkdir($this->_log_path);
                    }
                }
            }
        }

        /**
         * 
         * @return CLogger
         */
        public static function instance() {
            if (self::$_instance == NULL) {
                self::$_instance = new CLogger();
            }
            return self::$_instance;
        }

        public function write($message) {
            $this->__create_log_path();
            $filename = date("Ymd") . "_" . $this->_suffix_filename . self::__EXT;
            $this->_file_path = $this->_log_path . $filename;
            
            $this->__get_level_name_by_const();

            if ($this->_level >= self::DEBUG) {
                $trace = array_slice(debug_backtrace(), 1);
                $msg_trace = $this->__backtrace($trace);
                $message .= "\n" .$msg_trace . "\n";
            }

            if (!$fp = @fopen($this->_file_path, 'ab')) {
                return FALSE;
            }

            $message = date("H:i:s") . " " . $this->_level_name . " " . $message . "\n";

            flock($fp, LOCK_EX);
            fwrite($fp, $message);
            flock($fp, LOCK_UN);
            fclose($fp);

            @chmod($filepath, FILE_WRITE_MODE);
            return TRUE;
        }

        private function __backtrace($trace) {
            if (!is_array($trace)) return "";

            $output = array();

            foreach ($trace as $key => $t) {
//                if ($key > 2) break;
                $temp = "";
                if (isset($t['file']))
                        $temp .= $t['file'] . " " . $t['line'] . "\n";
                if (isset($t['class'])) $temp .= $t['class'] . $t['type'];

                $temp .= $t['function'] . '( ';

                if ($this->_level >= self::TRACE) {
                    if (isset($t['args']) && is_array($t['args'])) {
                        $sep = '';
                        while ($arg = array_shift($t['args'])) {
                            if (is_string($arg) AND is_file($arg)) {
                                // Remove docroot from filename
                                $arg = preg_replace('!^' . preg_quote(BASEPATH) . '!', '', $arg);
                            }

                            $temp .= $sep . print_r($arg, TRUE);

                            // Change separator to a comma
                            $sep = ', ';
                        }
                    }
                }
                $temp .= " )";
                $output[] = $temp;
            }

            return implode("\n", $output);
        }

        public function get_log_path() {
            return $this->_log_path;
        }

        public function set_log_path($_log_path) {
            $this->_log_path = $_log_path;
            return $this;
        }

        public function set_additional_path($_additional_path) {
            $this->_additional_path = $_additional_path;
            return $this;
        }

        public function set_suffix_filename($_suffix_filename) {
            $this->_suffix_filename = $_suffix_filename;
            $filename = date("Ymd") . "_" . $this->_suffix_filename . self::__EXT;
            $this->_file_path = $this->_log_path . $filename;
            return $this;
        }

        public function set_level($_level) {
            if (is_string($_level)) {
                $this->_level_name = strtoupper($_level);
                $this->__get_level_by_name();
            }
            else {
                $this->_level = $_level;
                $this->__get_level_name_by_const();
            }
            return $this;
        }

        private function __get_level_by_name() {
            switch ($this->_level_name) {
                case 'ALERT':
                    $this->_level = self::ALERT;
                    break;
                case 'CRITICAL':
                    $this->_level = self::CRITICAL;
                    break;
                case 'ERROR':
                    $this->_level = self::ERROR;
                    break;
                case 'WARNING':
                    $this->_level = self::WARNING;
                    break;
                case 'NOTICE':
                    $this->_level = self::NOTICE;
                    break;
                case 'INFO':
                    $this->_level = self::INFO;
                    break;
                case 'DEBUG':
                    $this->_level = self::DEBUG;
                    break;
                case 'TRACE':
                    $this->_level = self::TRACE;
                    break;
                default:
                    break;
            }
        }

        private function __get_level_name_by_const() {
            switch ($this->_level) {
                case self::ALERT:
                    $this->_level_name = 'ALERT';
                    break;
                case self::CRITICAL:
                    $this->_level_name = 'CRITICAL';
                    break;
                case self::ERROR:
                    $this->_level_name = 'ERROR';
                    break;
                case self::WARNING:
                    $this->_level_name = 'WARNING';
                    break;
                case self::NOTICE:
                    $this->_level_name = 'NOTICE';
                    break;
                case self::INFO:
                    $this->_level_name = 'INFO';
                    break;
                case self::DEBUG:
                    $this->_level_name = 'DEBUG';
                    break;
                case self::TRACE:
                    $this->_level_name = 'TRACE';
                    break;
                default:
                    $this->_level_name = 'INFO';
                    break;
            }
        }

    }
    