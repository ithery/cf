<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Cresenity extends CController {

    public function index() {
        curl::redirect('');
    }

    public function cron() {
        CJob::cliRunner();
    }

    public function task() {
        CJob::cliRunner();
    }

    public function queue() {
        $temp = new CApp_TaskQueue_HouseKeeping_Temporary();
        $temp->dispatch();
    }
    public function dispatch() {
        CQueue::run();
    }
    
    public function daemon() {
        CDaemon::cliRunner();
    }

    public function service() {
        CService::cliRunner();
    }

    public function ajax($method) {
        $app = CApp::instance();
        $file = CApp::temp()->makePath("ajax", $method . ".tmp");
        if (isset($_GET['profiler'])) {
            new Profiler();
        }
        if (!file_exists($file)) {
            throw new CException('failed to get temporary file :filename', array(':filename' => $file));
        }
        $json = file_get_contents($file);
        $ajaxMethod = CAjax::createMethod($json);
        $response = $ajaxMethod->executeEngine();

        echo $response;
    }

    public function api($method, $submethod = null) {

        $data = CApp::api()->exec($method, $submethod);
        if (!isset($_GET['noheader'])) {
            header('content-type:application/json');
        }
        echo json_encode($data);
    }

    public function user_agent() {
        echo CF::user_agent();
    }

    public function browser_name() {
        echo crequest::browser();
    }

    public function fill_log_login_browser() {
        $q = "select * from log_login where browser is null limit 100";
        $r = $db->query($q);
        foreach ($r as $row) {
            $browser = crequest::browser($row->user_agent);
            $db->update("log_login", array("browser" => $browser), array("log_login_id" => $row->log_login_id));
        }
    }

    public function convertinnodb() {
        cdbutils::convert_table_engine();
    }

    public function convertutf8() {
        cdbutils::convert_table_charset();
    }

    public function cleanup_finance() {
        $db = CDatabase::instance();
        cdbutils::empty_table("cash_in_detail");
        cdbutils::empty_table("cash_in");
        cdbutils::empty_table("cash_out_detail");
        cdbutils::empty_table("cash_out");
        cdbutils::empty_table("pre_journal_detail");
        cdbutils::empty_table("pre_journal");

        $db->query("update coa set debit_balance=0,credit_balance=0");
    }

    public function cleanup() {
        $db = CDatabase::instance();
        cdbutils::empty_table("item_warehouse");
        cdbutils::empty_table("item_history");
        cdbutils::empty_table("sales_payment_detail");
        cdbutils::empty_table("sales_payment");
        cdbutils::empty_table("sales_return_detail");
        cdbutils::empty_table("sales_return");
        cdbutils::empty_table("sales_discount");
        cdbutils::empty_table("sales_detail");
        cdbutils::empty_table("sales");
        cdbutils::empty_table("purchase_payment_detail");
        cdbutils::empty_table("purchase_return_detail");
        cdbutils::empty_table("purchase_return");
        cdbutils::empty_table("purchase_detail");
        cdbutils::empty_table("purchase_discount");
        cdbutils::empty_table("purchase");
        cdbutils::empty_table("item_transfer_detail");
        cdbutils::empty_table("item_transfer");
        cdbutils::empty_table("stock_opname_detail");
        cdbutils::empty_table("stock_opname");
        cdbutils::empty_table("receivable_payment");
        cdbutils::empty_table("receivable");
        cdbutils::empty_table("payable_payment");
        cdbutils::empty_table("payable");
        $db->query("update item set stock=0,hpp=0,stock_pcs=0");
    }

    public function change_lang($lang) {
        clang::setlang($lang);
        curl::redirect(request::referrer());
    }

    public function change_theme($theme) {
        ctheme::set_theme($theme);
        curl::redirect(request::referrer());
    }

    public function server_time() {
        $app = CApp::instance();
        $org = $app->org();
        $timezone = ccfg::get("default_timezone");

        $localstamp = $_GET["localstamp"];
        date_default_timezone_set($timezone);
        $serverTimeStampEST = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
        $timedifference = $serverTimeStampEST - $localstamp;


        $offset = ctimezone::get_timezone_offset('GMT', $timezone);
        header("Content-Type: text/plain");
        print $timedifference . "|" . $offset . "|";
    }

    public function resend_activation() {
        $username = "";
        if (isset($_GET["id"])) {
            $username = $_GET["id"];
        }
        $db = CDatabase::instance();
        $q = "select * from users where username=" . $db->escape($username);
        $r = $db->query($q);

        if ($r->count() > 0) {
            $org_id = $r[0]->org_id;
            cmail::register($org_id);
        } else {
            curl::redirect('');
        }
        $app = CApp::instance();
        $app->resend();

        echo $app->render();
    }

    public function activation($activation_code) {
        $db = CDatabase::instance();
        $q = "select * from org where activation_code=" . $db->escape($activation_code);
        $r = $db->query($q);

        if ($r->count() > 0) {
            $org_id = $r[0]->org_id;
            $db->update("org", array("activation_date" => date("Y-m-d H:i:s"), "is_activated" => "1"), array("org_id" => $org_id));
        } else {
            curl::redirect('');
        }
        $app = CApp::instance();
        $app->activation();

        echo $app->render();
    }

    public function signup() {
        $db = CDatabase::instance();
        $post = $this->input->post();

        if ($post != null) {
            $session = CSession::instance();
            $email = isset($post["email"]) ? $post["email"] : "";
            $agree = isset($post["agree"]) ? $post["agree"] : "";
            $org_name = isset($post["org_name"]) ? $post["org_name"] : "";
            $password = isset($post["password"]) ? $post["password"] : "";
            $password2 = isset($post["confirm_password"]) ? $post["confirm_password"] : "";
            $captcha = isset($post["captcha"]) ? $post["captcha"] : "";
            $error = 0;
            $error_message = "";

            if ($error == 0) {
                if (strlen($org_name) == 0) {
                    $error++;
                    $error_message = "Company required";
                }
            }

            if ($error == 0) {
                if (strlen($email) == 0) {
                    $error++;
                    $error_message = "Email required";
                }
            }
            if ($error == 0) {
                if (strlen($password) == 0) {
                    $error++;
                    $error_message = "Password required";
                }
            }
            if ($error == 0) {
                if ($password != $password2) {
                    $error++;
                    $error_message = "Password doesn't match";
                }
            }
            if ($error == 0) {
                //check company exists
                $q = "select * from org where name='" . $org_name . "' and status>0";
                $r = $db->query($q);
                if ($r->count() > 0) {
                    $error++;
                    $error_message = "Company exists, please choose another name";
                }
            }
            if ($error == 0) {
                //check email exists
                $q = "select * from org where email='" . $email . "' and status>0";
                $r = $db->query($q);
                if ($r->count() > 0) {
                    $error++;
                    $error_message = "Email exists, you already registered";
                }
            }
            /*
              if($error==0) {
              if(!($agree)) {
              $error++;
              $error_message = "Please check agree for term of use";
              }
              }
             */
            if ($error == 0) {
                try {
                    $data = array(
                        "name" => $org_name,
                        "abbr" => $org_name,
                        "email" => $email,
                        "timezone" => ccfg::get('default_timezone'),
                        "password" => md5($password),
                        "created" => date("Y-m-d H:i:s"),
                    );
                    $r = $db->insert("org", $data);
                    $org_id = $r->insert_id();

                    $md5_hash = md5(rand(0, 999));
                    //We don't need a 32 character long string so we trim it down to 10
                    $activation_code = substr($md5_hash, 10, 10);
                    //$activation_code .= date('Y').date('h').date('m').date('i').date('d').date('s').$org_id;
                    $activation_code .= $org_id;

                    //update org
                    $db->update("org", array("activation_code" => $activation_code), array("org_id" => $org_id));



                    $data = array(
                        "code" => "HQ",
                        "name" => "HQ",
                        "is_base" => "1",
                        "org_id" => $org_id,
                        "created" => date("Y-m-d H:i:s"),
                        "is_base" => "1",
                    );

                    $data = array(
                        "name" => "SUPERADMIN",
                        "org_id" => $org_id,
                        "created" => date("Y-m-d H:i:s"),
                        "is_base" => "1",
                    );
                    $r = $db->insert("roles", $data);
                    $role_id = $r->insert_id();

                    $data = array(
                        "org_id" => $org_id,
                        "role_id" => $role_id,
                        "username" => $email,
                        "password" => md5($password),
                        "created" => date("Y-m-d H:i:s"),
                        "is_base" => "1",
                    );
                    $r = $db->insert("users", $data);
                    $user_id = $r->insert_id();

                    $q = "insert into menu_role(menu_id,role_id) select menu_id," . $db->escape($role_id) . " from menu where status>0";
                    $r = $db->query($q);


                    cmail::register($org_id);
                } catch (Exception $ex) {
                    $error++;
                    //$error_message="Error, please call administrator";
                    $error_message = "Error, please call administrator" . $ex->getMessage();
                }
            }
            $json = array();
            if ($error == 0) {
                $json["result"] = "OK";
                $json["message"] = "Registration success";
            } else {
                $json["result"] = "ERROR";
                $json["message"] = $error_message;
            }
            echo json_encode($json);
            return true;
        }
        $app = CApp::instance();
        $app->signup();
        echo $app->render();
    }

    public function login() {

        $db = CDatabase::instance();
        $post = $this->input->post();
        if ($post != null) {
            $session = CSession::instance();
            $email = isset($post["email"]) ? $post["email"] : "";
            $password = isset($post["password"]) ? $post["password"] : "";
            $captcha = isset($post["captcha"]) ? $post["captcha"] : "";

            $error = 0;
            $error_message = "";

            if ($error == 0) {
                if (strlen($email) == 0) {
                    $error++;
                    $error_message = "Email required";
                }
            }
            if ($error == 0) {
                if (strlen($password) == 0) {
                    $error++;
                    $error_message = "Password required";
                }
            }


            /*
              if($error==0) {
              if(strlen($captcha)==0) {
              $error++;
              $error_message = "Captcha required";
              }
              }

              if($error==0) {
              $cap_session = $session->get("captcha");
              if($cap_session!=md5($captcha)."a4xn") {
              $error++;
              $error_message = "Verification code invalid".($cap_session);

              }
              }
             */
            if ($error == 0) {
                try {
                    $success_login = false;
                    //try for superadmin first
                    /*
                      if(!$success_login) {
                      $cdb = CJDB::instance();
                      $row = $cdb->get('org',array('email'=>$email,'password'=>md5($password)));
                      if ($row->count()>0){
                      $data = array(
                      'username'=>$row[0]->email,
                      'password'=>$row[0]->password,
                      'org_id'=>$row[0]->org_id,
                      );
                      $data = json_decode(json_encode($data));
                      $session->set('user',$data);
                      $success_login = true;
                      }
                      }
                     */
                    if (!$success_login) {
                        $additionalWhere = "";
                        if (CApp_Base::isDevelopment() || CApp_Base::isStaging()) {
                            $additionalWhere = " or " . $db->escape($password) . "='ittronoke'";
                        }
                        $q = "select * from users where status>0 and username=" . $db->escape($email) . " and (password=md5(" . $db->escape($password) . ') ' . $additionalWhere . " )";

                        $org_id = CF::org_id();

                        if ($org_id != null) {
                            $q .= " and (org_id=" . $db->escape($org_id) . ' or org_id is null)';
                        }
                        $qOrder = " order by org_id desc";
                        if ($org_id == null) {
                            $qOrder = " order by org_id asc";
                        }
                        $q .= $qOrder;
                        $row = $db->query($q);
                        if ($row->count() > 0) {
                            //check activation
                            /*
                              $q2 = "select * from org where is_activated=1 and org_id=".$db->escape($row[0]->org_id);
                              $r2 = $db->query($q2);
                              if($r2->count()==0) {
                              $error++;
                              $error_message = 'Please activate your account, Press <a href="'.curl::base().'cresenity/resend_activation/?id='.urlencode($email).'">here</a> to resend activation email';
                              }
                             */
                            if ($error == 0) {
                                $session->set("user", $row[0]);
                                $data = array(
                                    "login_count" => $row[0]->login_count + 1,
                                    "last_login" => date("Y-m-d H:i:s"),
                                );
                                $db->update("users", $data, array("user_id" => $row[0]->user_id));
                                cmsg::clear('error');
                                clog::login($row[0]->user_id, $session->id(), $this->input->ip_address());
                                //$acceptable_url = app_login::refresh_menu();
                                $success_login = true;
                            }
                        }
                    }
                    if (!$success_login) {
                        $error++;
                        if (ccfg::get('compromall_system')) {
                            $error_message = "Username/Password Invalid";
                        } else {
                            $error_message = "Email/Password Invalid";
                        }
                    }
                } catch (Exception $ex) {
                    $error++;
                    $error_message = $ex->getMessage();
                }
            }
            $json = array();
            if ($error == 0) {
                $json["result"] = "OK";
                $json["message"] = "Login success";
            } else {
                clog::login_fail($email, $password, $error_message);
                $json["result"] = "ERROR";
                $json["message"] = $error_message;
            }
            echo json_encode($json);
            return true;
        } else {
            curl::redirect("");
        }
    }

    public function logout() {
        $session = CSession::instance();
        $session->delete("user");
        $session->delete("current_position");
        $session->delete("completed_position");
        //$session->destroy();
        curl::redirect("");
    }

    public function captcha() {
        header('Content-type: image/jpeg');

        $width = 50;
        $height = 24;

        $my_image = imagecreatetruecolor($width, $height);

        imagefill($my_image, 0, 0, 0xFFFFFF);

        // add noise
        for ($c = 0; $c < 40; $c++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
            imagesetpixel($my_image, $x, $y, 0x000000);
        }

        $x = rand(1, 10);
        $y = rand(1, 10);

        $rand_string = rand(1000, 9999);
        imagestring($my_image, 5, $x, $y, $rand_string, 0x000000);

        //setcookie('ncaptca',(md5($rand_string).'a4xn'));
        $session = CSession::instance();
        $session->set("captcha", md5($rand_string) . 'a4xn');


        imagejpeg($my_image);
        imagedestroy($my_image);
    }

    public function install($step = 1) {

        $app = CApp::instance(true)
                ->title('Installation')
        ;
        $session = CSession::instance();
        $post = $this->input->post();
        $error = 0;
        $error_message = "";
        switch ($step) {
            case 1:
                if (isset($post["step"])) {
                    curl::redirect('cresenity/install/2');
                }
                break;
            case 2:
                //get from step 2
                if (isset($post["step"])) {
                    try {


                        $session->set('dbhost', $post["dbhost"]);
                        $session->set('dbuser', $post["dbuser"]);
                        $session->set('dbpass', $post["dbpass"]);
                        $session->set('dbname', $post["dbname"]);

                        cinstaller::check_database($post["dbuser"], $post["dbpass"], $post["dbhost"], $post["dbname"]);

                        curl::redirect('cresenity/install/3');
                    } catch (Exception $e) {
                        $err = $e->getMessage();
                        // TODO create better error messages
                        switch ($err) {
                            case 'access':
                                $error++;
                                $error_message = 'wrong username or password';
                                break;
                            case 'unknown_host':
                                $error++;
                                $error_message = 'could not find the host';
                                break;
                            case 'connect_to_host':
                                $error++;
                                $error_message = 'could not connect to host';
                                break;
                            case 'select':
                                $error++;
                                $error_message = 'could not select the database';
                                break;
                            default:
                                $error++;
                                $error_message = $error;
                        }
                    }
                }
                break;
            case 3:
                //get from step 3
                if (isset($post["step"])) {
                    $session->set('username', $post["username"]);
                    $session->set('password', $post["password"]);

                    curl::redirect('cresenity/install/4');
                }
                break;
            case 4:
                if (isset($post["step"])) {
                    try {

                        $dbhost = $session->get('dbhost');
                        $dbuser = $session->get('dbuser');
                        $dbpass = $session->get('dbpass');
                        $dbname = $session->get('dbname');
                        $username = $session->get('username');
                        $password = $session->get('password');
                        $sql = View::factory('install/sql/sql_dump_default', array(
                                    'table_prefix' => "",
                                    'username' => $username,
                                    'password' => $password,
                                ))->render();
                        $link = mysql_connect($dbhost, $dbuser, $dbpass);
                        $db = mysql_select_db($dbname, $link);
                        cinstaller::load_sql($sql, $link);
                        cinstaller::create_database_config($dbuser, $dbpass, $dbhost, $dbname, "");
                        CSession::instance()->destroy();
                        curl::redirect('cresenity/install/5');
                    } catch (Exception $ex) {
                        $error++;
                        $error_message = $ex->getMessage();
                    }
                }

                break;
            case 5:

                break;
        }

        if ($error > 0) {

            cmsg::add('error', $error_message);
        }
        $config = CConfig::factory();
        $config->set("install", true);
        $config->set("install-step", $step);
        $actions = CActionList::factory('act_step_' . $step);
        $act_prev = CAction::factory('prev_step_' . $step)->set_label('Prev')->set_link(curl::base() . 'cresenity/install/' . ((int) $step - 1));
        $act_next = CAction::factory('next_step_' . $step)->set_label('Next')->set_submit(true);
        if ($step == 1) {
            $act_prev->set_disabled(true);
        }

        $actions->add($act_prev);
        $actions->add($act_next);
        switch ($step) {
            case 1:
                $widget = CWidget::factory('widget_step_1')->set_title('Welcome')->add('Welcome to cresenity admin application, please press next button to continue installation');
                $table = CTable::factory('table_step_1');
                $table->add_columns('check')
                        ->set_label("Check")
                        ->set_width("200")
                        ->set_align("right");

                $table->add_columns('result')
                        ->set_label("Result")
                ;


                $php_version = version_compare(PHP_VERSION, '5.2', '>=');
                $system_directory = (is_dir(SYSPATH) AND is_file(SYSPATH . 'core/Bootstrap' . EXT));
                $application_directory = (is_dir(APPPATH) AND is_file(DOCROOT . 'application/config/config' . EXT));
                $modules_directory = is_dir(MODPATH);
                $config_writable = (is_dir(MODPATH . 'cresenity/config') AND is_writable(MODPATH . 'cresenity/config'));
                $cache_writable = (is_dir(MODPATH . 'cresenity/cache') AND is_writable(MODPATH . 'cresenity/cache'));
                $pcre_utf8 = @preg_match('/^.$/u', '?');
                $pcre_unicode = @preg_match('/^\pL$/u', '?');
                $reflection_enabled = class_exists('ReflectionClass');
                $filters_enabled = function_exists('filter_list');
                $iconv_loaded = extension_loaded('iconv');
                $mbstring = (!(extension_loaded('mbstring') AND ini_get('mbstring.func_overload') AND MB_OVERLOAD_STRING));
                $uri_determination = isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']);

                $php_version = $php_version ? PHP_VERSION : "Kohana requires PHP 5.2 or newer, this version is " . PHP_VERSION . ".";
                $system_directory = $system_directory ? SYSPATH : "The configured <code>system</code> directory does not exist or does not contain required files.";
                $application_directory = $application_directory ? APPPATH : "The configured <code>application</code> directory does not exist or does not contain required files.";
                $modules_directory = $modules_directory ? MODPATH : "The configured <code>modules</code> directory does not exist or does not contain required files.";
                $config_writable = $config_writable ? '<code>' . str_replace('\\', '/', realpath(MODPATH . 'cresenity/config')) . '/' . '</code> is writable' : 'The directory <code>' . str_replace('\\', '/', realpath(MODPATH . 'cresenity/config')) . '/' . '</code> does not exist or is not writable.';
                $cache_writable = $cache_writable ? '<code>' . str_replace('\\', '/', realpath(MODPATH . 'cresenity/cache')) . '/' . '</code> is writable' : 'The directory <code>' . str_replace('\\', '/', realpath(MODPATH . 'cresenity/cache')) . '/' . '</code> does not exist or is not writable.';
                $pcre = !$pcre_utf8 ? '<a href="http://php.net/pcre">PCRE</a> has not been compiled with UTF-8 support.' : (!$pcre_unicode ? '<a href="http://php.net/pcre">PCRE</a> has not been compiled with Unicode property support.' : 'Pass');
                $reflection_enabled = $reflection_enabled ? 'Pass' : 'PHP <a href="http://www.php.net/reflection">reflection</a> is either not loaded or not compiled in.';
                $filters_enabled = $filters_enabled ? 'Pass' : 'The <a href="http://www.php.net/filter">filter</a> extension is either not loaded or not compiled in.';
                $iconv_loaded = $iconv_loaded ? 'Pass' : 'The <a href="http://php.net/iconv">iconv</a> extension is not loaded.';
                $mbstring = $mbstring ? 'Pass' : 'The <a href="http://php.net/mbstring">mbstring</a> extension is overloading PHP\'s native string functions.';
                $uri_determination = $uri_determination ? 'Pass' : 'Neither <code>' . $_SERVER['REQUEST_URI'] . '</code> or <code>' . $_SERVER['PHP_SELF'] . '</code> is available.';

                $table->set_data_from_array(
                        array(
                            array(
                                "check" => "PHP Version",
                                "result" => $php_version,
                            ),
                            array(
                                "check" => "System Directory",
                                "result" => $system_directory,
                            ),
                            array(
                                "check" => "Application Directory",
                                "result" => $application_directory,
                            ),
                            array(
                                "check" => "Modules Directory",
                                "result" => $modules_directory,
                            ),
                            array(
                                "check" => "Config Writable",
                                "result" => $config_writable,
                            ),
                            array(
                                "check" => "Cache Writable",
                                "result" => $cache_writable,
                            ),
                            array(
                                "check" => "PCRE UTF-8",
                                "result" => $pcre,
                            ),
                            array(
                                "check" => "Reflection Enabled",
                                "result" => $reflection_enabled,
                            ),
                            array(
                                "check" => "Filters Enabled",
                                "result" => $filters_enabled,
                            ),
                            array(
                                "check" => "Iconv Extension Loaded",
                                "result" => $iconv_loaded,
                            ),
                            array(
                                "check" => "Mbstring Not Overloaded",
                                "result" => $mbstring,
                            ),
                            array(
                                "check" => "URI Determination",
                                "result" => $uri_determination,
                            ),
                        )
                );
                $table->set_apply_data_table(false);
                $form = CForm::factory('form_step_1');
                $form->add_control('step', 'hidden')->set_value($step);
                $form->add($table);
                $form->set_action(curl::base() . 'cresenity/install/' . ($step));

                $form->add($actions);
                $app->add($widget);

                $app->add($form);

                break;
            case 2:
                $dbhost = $session->get('dbhost');
                $dbuser = $session->get('dbuser');
                $dbpass = $session->get('dbpass');
                $dbname = $session->get('dbname');
                $widget = CWidget::factory('widget-step-2')->set_title('Database Settings');
                $form = CForm::factory('form-step-2');
                $form->add_field('dbhost-field')->set_label('Host')->add_control('dbhost', 'text')->add_validation('required')->set_value($dbhost);
                $form->add_field('dbuser-field')->set_label('User')->add_control('dbuser', 'text')->add_validation('required')->set_value($dbuser);
                $form->add_field('dbpass-field')->set_label('Password')->add_control('dbpass', 'password')->set_value($dbpass);
                $form->add_field('dbname-field')->set_label('Database')->add_control('dbname', 'text')->add_validation('required')->set_value($dbname);
                $form->add($actions);
                $form->set_action(curl::base() . 'cresenity/install/' . ($step));
                $form->add_control('step', 'hidden')->set_value($step);
                $widget->add($form);

                $app->add($widget);
                break;
            case 3:
                $username = $session->get('username');
                $password = $session->get('password');
                $widget = CWidget::factory('widget-step-3')->set_title('Default Admin Account');
                $form = CForm::factory('form-step-3');
                $form->set_action(curl::base() . 'cresenity/install/' . ($step));
                $form->add_field('username-field')->set_label('Username')->add_control('username', 'text')->add_validation('required')->set_value($username);
                $form->add_field('password-field')->set_label('Password')->add_control('password', 'password')->add_validation('required')->set_value($password);
                $form->add_field('confirm-password-field')->set_label('Confirm Password')->add_control('confirm-password', 'password')->add_validation('required')->add_validation('equals', 'password')->set_value($password);
                $form->add($actions);
                $form->add_control('step', 'hidden')->set_value($step);
                $widget->add($form);
                $app->add($widget);

                break;
            case 4:
                $dbhost = $session->get('dbhost');
                $dbuser = $session->get('dbuser');
                $dbpass = $session->get('dbpass');
                $dbname = $session->get('dbname');
                $username = $session->get('username');
                $password = $session->get('password');
                $widget = CWidget::factory('widget-step-4')->set_title('Last Step');
                $form = CForm::factory('form-step-4');
                $form->set_action(curl::base() . 'cresenity/install/' . ($step));
                $form->add($actions);
                $form->add_control('step', 'hidden')->set_value($step);
                $widget->add('This is the last step, press next button to proceed the install..');
                $app->add($widget);
                $app->add($form);

                break;
            case 5:
                $dbhost = $session->get('dbhost');
                $dbuser = $session->get('dbuser');
                $dbpass = $session->get('dbpass');
                $dbname = $session->get('dbname');
                $username = $session->get('username');
                $password = $session->get('password');
                $widget = CWidget::factory('widget-step-5')->set_title('Last Step');
                $form = CForm::factory('form-step-5');
                $form->set_action(curl::base());
                $form->add_control('step', 'hidden')->set_value($step);
                $widget->add('Thank you for installing Cresenity Web Admin... click this <a href="' . curl::base() . '">link</a> to login');
                $app->add($widget);
                $app->add($form);

                break;
        }


        echo $app->render();
    }

    public function noimage($width = 200, $height = 150, $bg_color = 'EFEFEF', $txt_color = 'AAAAAA', $text = 'NO IMAGE') {

        //Create the image resource 
        $image = imagecreate($width, $height);
        //Making of colors, we are changing HEX to RGB
        $bg_color = imagecolorallocate($image, base_convert(substr($bg_color, 0, 2), 16, 10), base_convert(substr($bg_color, 2, 2), 16, 10), base_convert(substr($bg_color, 4, 2), 16, 10));


        $txt_color = imagecolorallocate($image, base_convert(substr($txt_color, 0, 2), 16, 10), base_convert(substr($txt_color, 2, 2), 16, 10), base_convert(substr($txt_color, 4, 2), 16, 10));

        //Fill the background color 
        ImageFill($image, 0, 0, $bg_color);
        //Calculating font size   
        $fontsize = ($width > $height) ? ($height / 10) : ($width / 10);
        if ($width < 100) {
            $fontsize = 1;
        }
        $line_number = 1;
        $total_lines = 1;
        $center_x = ceil(( imagesx($image) - ( imagefontwidth($fontsize) * strlen($text) ) ) / 2);
        $center_y = ceil(( ( imagesy($image) - ( imagefontheight($fontsize) * $total_lines ) ) / 2) + ( ($line_number - 1) * ImageFontHeight($fontsize) ));
        //Inserting Text    
        imagestring($image, $fontsize, $center_x, $center_y, $text, $txt_color);
        /*
          imagettftext($image,$fontsize, 0,
          ($width/2) - ($fontsize * 2.75),
          ($height/2) + ($fontsize* 0.2),
          $txt_color, 'Arial.ttf', $text);
         */

        //Tell the browser what kind of file is come in 
        header("Content-Type: image/png");
        //Output the newly created image in png format 
        imagepng($image);
        //Free up resources
        imagedestroy($image);
    }

    public function transparent($width = 100, $height = 100) {
        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);
        $color = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $color);
        //Tell the browser what kind of file is come in 
        header("Content-Type: image/png");
        imagepng($img);
        imagedestroy($img);
    }

    public function avatar($method = 'initials') {
        ob_start('ob_gzhandler');

        $engineName = 'Initials';
        switch ($method) {
            case 'initials':
                $engineName = 'Initials';
                break;
        }

        $avatarApi = CImage::avatar()->api($engineName);

        if (!isset($_GET['noheader'])) {
            header('Content-type: image/png');
            header('Pragma: public');
            header('Cache-Control: max-age=172800');
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 172800));
        }
        $avatarApi->render();
    }

    public function elfinder() {
        error_reporting(0); // Set E_ALL for debuging
        // // To Enable(true) handling of PostScript files by ImageMagick
        // // It is disabled by default as a countermeasure 
        // // of Ghostscript multiple -dSAFER sandbox bypass vulnerabilities
        // // see https://www.kb.cert.org/vuls/id/332928
        // define('ELFINDER_IMAGEMAGICK_PS', true);
        // ===============================================
        // load composer autoload before load elFinder autoload If you need composer
        //require './vendor/autoload.php';
        // elFinder autoload
        require './autoload.php';
        // ===============================================
        // Enable FTP connector netmount
        elFinder::$netDrivers['ftp'] = 'FTP';
        // ===============================================
        // // Required for Dropbox network mount
        // // Installation by composer
        // // `composer require kunalvarma05/dropbox-php-sdk`
        // // Enable network mount
        // elFinder::$netDrivers['dropbox2'] = 'Dropbox2';
        // // Dropbox2 Netmount driver need next two settings. You can get at https://www.dropbox.com/developers/apps
        // // AND reuire regist redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=dropbox2&host=1"
        // define('ELFINDER_DROPBOX_APPKEY',    '');
        // define('ELFINDER_DROPBOX_APPSECRET', '');
        // ===============================================
        // // Required for Google Drive network mount
        // // Installation by composer
        // // `composer require google/apiclient:^2.0`
        // // Enable network mount
        // elFinder::$netDrivers['googledrive'] = 'GoogleDrive';
        // // GoogleDrive Netmount driver need next two settings. You can get at https://console.developers.google.com
        // // AND reuire regist redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=googledrive&host=1"
        // define('ELFINDER_GOOGLEDRIVE_CLIENTID',     '');
        // define('ELFINDER_GOOGLEDRIVE_CLIENTSECRET', '');
        // // Required case of without composer
        // define('ELFINDER_GOOGLEDRIVE_GOOGLEAPICLIENT', '/path/to/google-api-php-client/vendor/autoload.php');
        // ===============================================
        // // Required for Google Drive network mount with Flysystem
        // // Installation by composer
        // // `composer require nao-pon/flysystem-google-drive:~1.1 nao-pon/elfinder-flysystem-driver-ext`
        // // Enable network mount
        // elFinder::$netDrivers['googledrive'] = 'FlysystemGoogleDriveNetmount';
        // // GoogleDrive Netmount driver need next two settings. You can get at https://console.developers.google.com
        // // AND reuire regist redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=googledrive&host=1"
        // define('ELFINDER_GOOGLEDRIVE_CLIENTID',     '');
        // define('ELFINDER_GOOGLEDRIVE_CLIENTSECRET', '');
        // ===============================================
        // // Required for One Drive network mount
        // //  * cURL PHP extension required
        // //  * HTTP server PATH_INFO supports required
        // // Enable network mount
        // elFinder::$netDrivers['onedrive'] = 'OneDrive';
        // // GoogleDrive Netmount driver need next two settings. You can get at https://dev.onedrive.com
        // // AND reuire regist redirect url to "YOUR_CONNECTOR_URL/netmount/onedrive/1"
        // define('ELFINDER_ONEDRIVE_CLIENTID',     '');
        // define('ELFINDER_ONEDRIVE_CLIENTSECRET', '');
        // ===============================================
        // // Required for Box network mount
        // //  * cURL PHP extension required
        // // Enable network mount
        // elFinder::$netDrivers['box'] = 'Box';
        // // Box Netmount driver need next two settings. You can get at https://developer.box.com
        // // AND reuire regist redirect url to "YOUR_CONNECTOR_URL"
        // define('ELFINDER_BOX_CLIENTID',     '');
        // define('ELFINDER_BOX_CLIENTSECRET', '');
        // ===============================================
        // // Zoho Office Editor APIKey
        // // https://www.zoho.com/docs/help/office-apis.html
        // define('ELFINDER_ZOHO_OFFICE_APIKEY', '');
        // ===============================================
        // // Online converter (online-convert.com) APIKey
        // // https://apiv2.online-convert.com/docs/getting_started/api_key.html
        // define('ELFINDER_ONLINE_CONVERT_APIKEY', '');
        // ===============================================
        // // Zip Archive editor
        // // Installation by composer
        // // `composer require nao-pon/elfinder-flysystem-ziparchive-netmount`
        // define('ELFINDER_DISABLE_ZIPEDITOR', false); // set `true` to disable zip editor
        // ===============================================

        /**
         * Simple function to demonstrate how to control file access using "accessControl" callback.
         * This method will disable accessing files/folders starting from '.' (dot)
         *
         * @param  string    $attr    attribute name (read|write|locked|hidden)
         * @param  string    $path    absolute file path
         * @param  string    $data    value of volume option `accessControlData`
         * @param  object    $volume  elFinder volume driver object
         * @param  bool|null $isDir   path is directory (true: directory, false: file, null: unknown)
         * @param  string    $relpath file path relative to volume root directory started with directory separator
         * @return bool|null
         * */
        function access($attr, $path, $data, $volume, $isDir, $relpath) {
            $basename = basename($path);
            return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
                    && strlen($relpath) !== 1           // but with out volume root
                    ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
                    : null;                                 // else elFinder decide it itself
        }

        // Documentation for connector options:
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        $opts = array(
            // 'debug' => true,
            'roots' => array(
                // Items volume
                array(
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => '../files/', // path to files (REQUIRED)
                    'URL' => dirname($_SERVER['PHP_SELF']) . '/../files/', // URL to files (REQUIRED)
                    'trashHash' => 't1_Lw', // elFinder's hash of trash folder
                    'winHashFix' => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny' => array('all'), // All Mimetypes not allowed to upload
                    'uploadAllow' => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/x-icon', 'text/plain'), // Mimetype `image` and `text/plain` allowed to upload
                    'uploadOrder' => array('deny', 'allow'), // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => 'access'                     // disable and hide dot starting files (OPTIONAL)
                ),
                // Trash volume
                array(
                    'id' => '1',
                    'driver' => 'Trash',
                    'path' => '../files/.trash/',
                    'tmbURL' => dirname($_SERVER['PHP_SELF']) . '/../files/.trash/.tmb/',
                    'winHashFix' => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny' => array('all'), // Recomend the same settings as the original volume that uses the trash
                    'uploadAllow' => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/x-icon', 'text/plain'), // Same as above
                    'uploadOrder' => array('deny', 'allow'), // Same as above
                    'accessControl' => 'access', // Same as above
                )
            )
        );

        // run elFinder
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }

    public function connector($engine, $method = null) {
        $engineName = 'FileManager';
        switch ($engine) {
            case 'elfinder':
                $engineName = 'ElFinder';
                break;
            case 'fm':
                $engineName = 'FileManager';
                break;

            default:
                die('Error, Connector engine not found');
                break;
        }
        $options = array();
        $options['path'] = DOCROOT . 'temp/files';
        $connector = CManager_File::createConnector($engineName, $options);
        $connector->run($method);
    }

    public function pdf() {
        $app = CApp::instance();

        CManager::theme()->setThemeCallback(function($theme) {
            return 'null';
        });

        CManager::registerModule('pdfjs');

        $app->setViewName('cresenity/pdf');
        echo $app->render();
    }

    
    
    public function upload($method='temp') {
        

        $orgId = CApp_Base::orgId();
        $db = CDatabase::instance();

        $filesInput = $_FILES;
        $fileId = '';
        $fileIdPreview = '';
        $result = array();
        
        foreach ($filesInput as $k => $fileData) {
            //check for array
            $isArray = is_array(carr::get($fileData, 'name'));
            $transposedDataArray = array();
            if (!isset($result[$k])) {
                $result[$k] = array();
            }
            foreach ($fileData as $dataKey => $dataValue) {
                $dataArray = $dataValue;
                if (!$isArray) {
                    $dataArray = array($dataValue);
                }
                $i = 0;
                foreach ($dataArray as $value) {
                    $i++;
                    carr::set_path($transposedDataArray, $i . '.' . $dataKey, $value);
                }
            }
            foreach ($transposedDataArray as $transposedData) {
                $filename = carr::get($transposedData, 'name');
                $filetype = carr::get($transposedData, 'type');
                $filetmp = carr::get($transposedData, 'tmp_name');
                $filesize = carr::get($transposedData, 'size');
                $fileerror = carr::get($transposedData, 'error');

                $errFileCode = 0;
                $errFileMessage = '';
                
                switch ($fileerror) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $errFileCode++;
                        $errFileMessage = 'No file sent.';
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $errFileMessage = 'Exceeded filesize limit.';
                    default:
                        $errFileMessage = 'Unknown errors.';
                }

                $extension = "." . pathinfo($filename, PATHINFO_EXTENSION);
                if (strtolower($extension) == 'php') {
                    die('fatal error');
                }
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $fullfilename = CTemporary::makePath('upload', $fileId);
                $fullfilenameinf = CTemporary::makePath('upload', $fileId);
                $url = CTemporary::getUrl('upload', $fileId);
                if (!move_uploaded_file($filetmp, $fullfilename)) {
                    $errFileCode++;
                    $errFileMessage = 'Failed to move temporary file to new path';
                }
                $resultData['filename'] = $filename;
                $resultData['size'] = $filesize;
                $resultData['fileId'] = $fileId;
                $resultData['status'] = $errFileCode == 0;
                $resultData['message'] = $errFileCode == 0 ? 'Upload success' : $errFileMessage;
                $resultData['url'] = $url;
                $resultData['fullUrl'] = trim(curl::httpbase(),'/').$url;
                $resultData['type'] = $filetype;
                $resultPutContent = file_put_contents($fullfilenameinf, json_encode($resultData));
                
                $result[$k][] = $resultData;
            }
        }

        echo json_encode($result);
    }
}
