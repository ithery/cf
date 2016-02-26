<?php

    class clog {

        public static function login($user_id) {
            $app = CApp::instance();
            $app_id = $app->app_id();
            $db = CDatabase::instance();
            $ip_address = crequest::remote_address();
            $session_id = csess::session_id();
            $browser = crequest::browser();
            $browser_version = crequest::browser_version();
            $platform = crequest::platform();
            $platform_version = crequest::platform_version();
            $user = cuser::get($user_id);
            $org_id = $user->org_id;
            $data = array(
                "login_date" => date("Y-m-d H:i:s"),
                "org_id" => $org_id,
                "user_agent" => CF::user_agent(),
                "browser" => $browser,
                "browser_version" => $browser_version,
                "platform" => $platform,
                "platform_version" => $platform_version,
                "remote_addr" => $ip_address,
                "user_id" => $user_id,
                "session_id" => $session_id,
                "app_id" => $app_id,
            );
            $db->insert("log_login", $data);
        }

        public static function login_fail($username, $password, $error_message) {
            $app = CApp::instance();
            $app_id = $app->app_id();
            $db = CDatabase::instance();
            $ip_address = crequest::remote_address();
            $session_id = csess::session_id();
            $browser = crequest::browser();
            $browser_version = crequest::browser_version();
            $platform = crequest::platform();
            $platform_version = crequest::platform_version();
            $data = array(
                "login_fail_date" => date("Y-m-d H:i:s"),
                "org_id" => null,
                "user_agent" => CF::user_agent(),
                "username" => $username,
                "password" => $password,
                "error_message" => $error_message,
                "browser" => $browser,
                "browser_version" => $browser_version,
                "platform" => $platform,
                "platform_version" => $platform_version,
                "remote_addr" => $ip_address,
                "session_id" => $session_id,
                "app_id" => $app_id,
            );
            $db->insert("log_login_fail", $data);
        }

        public static function log_print($user_id, $print_mode, $printer_type, $printer_name, $data_type, $print_ref_id, $print_ref_code) {
            $app = CApp::instance();
            $app_id = $app->app_id();
            $db = CDatabase::instance();
            $user = cuser::get($user_id);
            $user_id = $user->user_id;
            $org_id = $user->org_id;
            $ip_address = crequest::remote_address();
            $browser = crequest::browser();
            $browser_version = crequest::browser_version();
            $platform = crequest::platform();
            $platform_version = crequest::platform_version();


            $data = array(
                "print_date" => date("Y-m-d H:i:s"),
                "org_id" => $org_id,
                "session_id" => csess::session_id(),
                "user_agent" => CF::user_agent(),
                "browser" => $browser,
                "browser_version" => $browser_version,
                "platform" => $platform,
                "platform_version" => $platform_version,
                "remote_addr" => $ip_address,
                "user_id" => $user_id,
                "print_mode" => $print_mode,
                "printer_type" => $printer_type,
                "printer_name" => $printer_name,
                "data_type" => $data_type,
                "print_ref_id" => $print_ref_id,
                "print_ref_code" => $print_ref_code,
                "app_id" => $app_id,
            );
            $db->insert("log_print", $data);
        }

        public static function request($user_id=null) {
            $app = CApp::instance();
            $db = CDatabase::instance();
            $app_id = CF::app_id();
            $org_id = CF::org_id();
            $org = $app->org();
            if ($org != null) {
                $org_id = $org->org_id;
            }

            //no need to log ajax request
            if (crequest::is_ajax()) return false;
            //no need to log on administrator page
            $router_uri = CFRouter::routed_uri(CFRouter::$current_uri);
            $rsegment = explode('/', $router_uri);
            if (count($rsegment) > 0) {
                if ($rsegment[0] == "admin") {
                    return false;
                }
            }


            $nav = cnav::nav();

            $nav_name = "";
            $nav_label = "";
            $action_label = "";
            $action_name = "";
            $controller = crouter::controller();
            if ($controller == "cresenity") return false;
            $method = crouter::method();
            if ($nav != null) {
                $nav_name = $nav["name"];
                $nav_label = $nav["label"];

                if (isset($nav["action"])) {
                    foreach ($nav["action"] as $act) {
                        if (isset($act["controller"]) && isset($act["method"]) && $act["controller"] == $controller && $act["method"] == $method) {
                            $action_name = $act["name"];
                            $action_label = $act["label"];
                        }
                    }
                }
            }
            $db = CDatabase::instance();
            $ip_address = crequest::remote_address();
            $session_id = csess::session_id();
            $browser = crequest::browser();
            $browser_version = crequest::browser_version();
            $platform = crequest::platform();
            $platform_version = crequest::platform_version();
            $description = CF::domain();

            $data = array(
                "request_date" => date("Y-m-d H:i:s"),
                "org_id" => $org_id,
                "session_id" => csess::session_id(),
                "user_agent" => CF::user_agent(),
                "browser" => $browser,
                "browser_version" => $browser_version,
                "platform" => $platform,
                "platform_version" => $platform_version,
                "remote_addr" => $ip_address,
                "user_id" => $user_id,
                "uri" => crouter::complete_uri(),
                "routed_uri" => crouter::routed_uri(),
                "controller" => crouter::controller(),
                "method" => crouter::method(),
                "query_string" => crouter::query_string(),
                "nav" => $nav_name,
                "nav_label" => $nav_label,
                "action" => $action_name,
                "action_label" => $action_label,
                "description" => $description,
                "app_id" => $app_id,
            );
            $db->insert("log_request", $data);
        }

        
        public static function activity($param, $activity_type = "", $description = "") {

            $data_before = array();
            $data_after = array();
            if (!is_array($param)) {
                $user_id = $param;
            }
            else {
                $user_id = carr::get($param, 'user_id');
                $data_before = carr::get($param, 'before', array());
                $data_after = carr::get($param, 'after', array());
            }
            
            $data_before = json_encode($data_before);
            $data_after = json_encode($data_after);
            
            $db = CDatabase::instance();
            $app = CApp::instance();
            $app_id = $app->app_id();
            $db = CDatabase::instance();
            $app = CApp::instance();
            $ip_address = crequest::remote_address();
            $session_id = csess::session_id();
            $browser = crequest::browser();
            $browser_version = crequest::browser_version();
            $platform = crequest::platform();
            $platform_version = crequest::platform_version();
            $nav_name = "";
            $nav_label = "";
            $action_label = "";
            $action_name = "";
            $controller = crouter::controller();
            if ($controller == "cresenity") return false;
            $method = crouter::method();
            $nav = cnav::nav();
            if ($nav != null) {
                $nav_name = $nav["name"];
                $nav_label = $nav["label"];
                if (isset($nav["action"])) {
                    foreach ($nav["action"] as $act) {
                        if (isset($act["controller"]) && isset($act["method"]) && $act["controller"] == $controller && $act["method"] == $method) {
                            $action_name = $act["name"];
                            $action_label = $act["label"];
                        }
                    }
                }
            }
            $user = cuser::get($user_id);
            $org_id = $user->org_id;
            $data = array(
                "activity_date" => date("Y-m-d H:i:s"),
                "org_id" => $org_id,
                "session_id" => csess::session_id(),
                "user_agent" => CF::user_agent(),
                "browser" => $browser,
                "browser_version" => $browser_version,
                "platform" => $platform,
                "platform_version" => $platform_version,
                "remote_addr" => $ip_address,
                "user_id" => $user_id,
                "uri" => crouter::complete_uri(),
                "routed_uri" => crouter::routed_uri(),
                "controller" => crouter::controller(),
                "method" => crouter::method(),
                "query_string" => crouter::query_string(),
                "nav" => $nav_name,
                "nav_label" => $nav_label,
                "action" => $action_name,
                "action_label" => $action_label,
                "activity_type" => $activity_type,
                "description" => $description,
                "app_id" => $app_id,
                "data_before" => $data_before,
                "data_after" => $data_after,
            );
            $db->insert("log_activity", $data);
        }

        public static function sync_error($org_id, $store_id, $session_id, $message) {

            $db = CDatabase::instance();

            $data = array(
                "log_date" => date("Y-m-d H:i:s"),
                "org_id" => $org_id,
                "store_id" => $store_id,
                "session_id" => $session_id,
                "message" => $message,
            );
            $db->insert("log_sync_error", $data);
        }

        public static function backup($user_id, $filename, $directory = "") {

            $db = CDatabase::instance();
            $app = CApp::instance();
            $org = $app->org();
            $org_id = null;
            if ($org != null) {
                $org_id = $org->org_id;
            }
            $data = array(
                "backup_date" => date("Y-m-d H:i:s"),
                "org_id" => $org_id,
                "user_id" => $user_id,
                "dir" => $directory,
                "filename" => $filename,
                "app_id" => CF::config('cresenity.app_id'),
            );
            $db->insert("log_backup", $data);
        }

        public static function cleanup($user_id) {

            $db = CDatabase::instance();
            $app = CApp::instance();
            $org = $app->org();
            $org_id = null;
            $data = array(
                "cleanup_date" => date("Y-m-d H:i:s"),
                "org_id" => $org_id,
                "user_id" => $user_id,
                "app_id" => CF::config('cresenity.app_id'),
            );
            $db->insert("log_cleanup", $data);
        }

        public static function log($filename, $type, $message) {
            $date = date("Y-m-d H:i:s");
            $str = $date . " " . $type . " " . $message . "\r\n";
            $filename = DOCROOT . "/log/" . date("Ymd") . "_" . $filename;
            $fh = @fopen($filename, 'a+');
            fwrite($fh, $str);
            @fclose($fh);
        }

        /**
         * This function is used for log for any statement. <br/>
         * Here is inline an example: 
         * <pre>
         *  <code>
         *      <?php clog::write('Test');?>
         *  </code>
         * </pre>
         * 
         * @param array/string $options     
         * @return boolean
         */
        public static function write($options) {
            $clogger_instance = CLogger::instance();

            $message = $options;
            if (is_array($options)) {
                $message = carr::get($options, 'message');
                $filename = carr::get($options, 'filename');
                $level = carr::get($options, 'level');
                $path = carr::get($options, 'path');

                if (strlen($filename) > 0)
                        $clogger_instance->set_suffix_filename($filename);
                if (strlen($level) > 0) $clogger_instance->set_level($level);
                if (strlen($path) > 0) $clogger_instance->set_additional_path($path);
            }
            return $clogger_instance->write($message);
        }

    }

?>