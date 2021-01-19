<?php

defined('SYSPATH') or die('No direct access allowed.');

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

    public function dispatch() {
        CQueue::run();
    }

    public function daemon() {
        CDaemon::cliRunner();
    }

    public function component() {
        return CApp::component()->controllerHandler(func_get_args());
    }

    public function qc($className = null) {
        if ($className == null) {
            CF::show404();
            return;
        }
        CQC::cliRunner($className);
    }

    public function ajax() {
        $args = func_get_args();
        $method = carr::get($args, 0);
        $app = CApp::instance();
        $filename = $method . '.tmp';
        $file = CTemporary::getPath('ajax', $filename);

        if (isset($_GET['profiler'])) {
            new CProfiler();
        }
        $disk = CTemporary::disk();
        if (!$disk->exists($file)) {
            throw new CException('failed to get temporary file :filename', [':filename' => $file]);
        }
        $json = $disk->get($file);

        $ajaxMethod = CAjax::createMethod($json)->setArgs($args);
        $response = $ajaxMethod->executeEngine();

        return $response;
    }

    public function api($method, $submethod = null) {
        $data = CApp::api()->exec($method, $submethod);
        if (!isset($_GET['noheader'])) {
            header('content-type:application/json');
        }
        echo json_encode($data);
    }

    //@codingStandardsIgnoreStart

    /**
     * change lang
     *
     * @param string $lang
     *
     * @return void
     *
     * @deprecated version
     */
    public function change_lang($lang) {
        clang::setlang($lang);
        curl::redirect(crequest::referrer());
    }

    public function change_theme($theme) {
        CManager::theme()->setTheme($theme);
        curl::redirect(crequest::referrer());
    }

    //@codingStandardsIgnoreEnd

    /**
     * Default login action
     *
     * @return void
     */
    public function login() {
        $db = CDatabase::instance();
        $post = $this->input->post();
        if ($post != null) {
            $session = CSession::instance();
            $email = isset($post['email']) ? $post['email'] : '';
            $password = isset($post['password']) ? $post['password'] : '';
            $captcha = isset($post['captcha']) ? $post['captcha'] : '';

            $error = 0;
            $error_message = '';

            if ($error == 0) {
                if (strlen($email) == 0) {
                    $error++;
                    $error_message = 'Email required';
                }
            }
            if ($error == 0) {
                if (strlen($password) == 0) {
                    $error++;
                    $error_message = 'Password required';
                }
            }

            if ($error == 0) {
                try {
                    $success_login = false;

                    if (!$success_login) {
                        $additionalWhere = '';
                        if (CApp_Base::isDevelopment() || CApp_Base::isStaging()) {
                            $additionalWhere = ' or ' . $db->escape($password) . "='ittronoke'";
                        }
                        $q = 'select * from users where status>0 and username=' . $db->escape($email) . ' and (password=md5(' . $db->escape($password) . ') ' . $additionalWhere . ' )';

                        $org_id = CF::orgId();

                        if ($org_id != null) {
                            $q .= ' and (org_id=' . $db->escape($org_id) . ' or org_id is null)';
                        }
                        $qOrder = ' order by org_id desc';
                        if ($org_id == null) {
                            $qOrder = ' order by org_id asc';
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
                                $session->set('user', $row[0]);
                                $data = [
                                    'login_count' => $row[0]->login_count + 1,
                                    'last_login' => date('Y-m-d H:i:s'),
                                ];
                                $db->update('users', $data, ['user_id' => $row[0]->user_id]);
                                cmsg::clear('error');
                                clog::login($row[0]->user_id, $session->id(), $this->input->ip_address());
                                //$acceptable_url = app_login::refresh_menu();
                                $success_login = true;
                            }
                        }
                    }
                    if (!$success_login) {
                        $error++;
                        $error_message = 'Email/Password Invalid';
                    }
                } catch (Exception $ex) {
                    $error++;
                    $error_message = $ex->getMessage();
                }
            }
            $json = [];
            if ($error == 0) {
                $json['result'] = 'OK';
                $json['message'] = 'Login success';
            } else {
                clog::login_fail($email, $password, $error_message);
                $json['result'] = 'ERROR';
                $json['message'] = $error_message;
            }
            echo json_encode($json);
            return true;
        } else {
            curl::redirect('');
        }
    }

    public function logout() {
        $session = CSession::instance();
        $session->delete('user');
        $session->delete('current_position');
        $session->delete('completed_position');
        //$session->destroy();
        curl::redirect('');
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
        $session->set('captcha', md5($rand_string) . 'a4xn');

        imagejpeg($my_image);
        imagedestroy($my_image);
    }

    public function noimage($width = 200, $height = 150, $bg_color = 'EFEFEF', $txt_color = 'AAAAAA', $text = 'NO IMAGE') {
        //Create the image resource
        $image = imagecreate($width, $height);
        //Making of colors, we are changing HEX to RGB
        $bg_color = imagecolorallocate($image, base_convert(substr($bg_color, 0, 2), 16, 10), base_convert(substr($bg_color, 2, 2), 16, 10), base_convert(substr($bg_color, 4, 2), 16, 10));

        $txt_color = imagecolorallocate($image, base_convert(substr($txt_color, 0, 2), 16, 10), base_convert(substr($txt_color, 2, 2), 16, 10), base_convert(substr($txt_color, 4, 2), 16, 10));

        //Fill the background color
        imagefill($image, 0, 0, $bg_color);
        //Calculating font size
        $fontsize = ($width > $height) ? ($height / 10) : ($width / 10);
        if ($width < 100) {
            $fontsize = 1;
        }
        $line_number = 1;
        $total_lines = 1;
        $center_x = ceil((imagesx($image) - (imagefontwidth($fontsize) * strlen($text))) / 2);
        $center_y = ceil(((imagesy($image) - (imagefontheight($fontsize) * $total_lines)) / 2) + (($line_number - 1) * ImageFontHeight($fontsize)));
        //Inserting Text
        imagestring($image, $fontsize, $center_x, $center_y, $text, $txt_color);
        /*
          imagettftext($image,$fontsize, 0,
          ($width/2) - ($fontsize * 2.75),
          ($height/2) + ($fontsize* 0.2),
          $txt_color, 'Arial.ttf', $text);
         */
        //Tell the browser what kind of file is come in
        header('Content-Type: image/png');
        //Output the newly created image in png format
        imagepng($image);
        //Free up resources
        imagedestroy($image);

        /*
        //Tell the browser what kind of file is come in
        $headers = [
            'Content-Type' => 'image/png',
        ];

        return c::response()->stream(function () use ($image) {
            //Output the newly created image in png format
            imagepng($image);
            //Free up resources
            imagedestroy($image);
        }, 200, $headers);
        */
    }

    public function transparent($width = 100, $height = 100) {
        $img = imagecreatetruecolor($width, $height);
        imagesavealpha($img, true);
        $color = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $color);
        //Tell the browser what kind of file is come in
        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
    }

    public function avatar($method = 'initials') {
        if (!function_exists('gd_info')) {
            throw new Exception('GD Library extension must be installed/enabled to use avatar endpoint.');
        }
        if (!function_exists('finfo_buffer')) {
            throw new Exception('PHP fileinfo extension must be installed/enabled to use avatar endpoint.');
        }

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
        $options = [];
        $options['path'] = DOCROOT . 'temp/files';
        $connector = CManager_File::createConnector($engineName, $options);
        $connector->run($method);
    }

    public function pdf() {
        $app = CApp::instance();

        CManager::theme()->setThemeCallback(function ($theme) {
            return 'null';
        });

        CManager::registerModule('pdfjs');

        $app->setViewName('cresenity/pdf');
        echo $app->render();
    }

    public function upload($method = 'temp') {
        $orgId = CApp_Base::orgId();
        $db = CDatabase::instance();

        $filesInput = $_FILES;
        $fileId = '';
        $fileIdPreview = '';
        $result = [];

        foreach ($filesInput as $k => $fileData) {
            //check for array
            $isArray = is_array(carr::get($fileData, 'name'));
            $transposedDataArray = [];
            if (!isset($result[$k])) {
                $result[$k] = [];
            }
            foreach ($fileData as $dataKey => $dataValue) {
                $dataArray = $dataValue;
                if (!$isArray) {
                    $dataArray = [$dataValue];
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
                        // no break
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $errFileMessage = 'Exceeded filesize limit.';
                        // no break
                    default:
                        $errFileMessage = 'Unknown errors.';
                }

                $extension = '.' . pathinfo($filename, PATHINFO_EXTENSION);
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
                $resultData['fullUrl'] = trim(curl::httpbase(), '/') . $url;
                $resultData['type'] = $filetype;
                $resultPutContent = file_put_contents($fullfilenameinf, json_encode($resultData));

                $result[$k][] = $resultData;
            }
        }

        echo json_encode($result);
    }

    public function qrcode() {
        $request = $_GET;
        $data = carr::get($request, 'd');
        $options = [];
        $options['s'] = carr::get($request, 's', 'qr');
        $qrcode = new CImage_QRCode($data, $options);
        $qrcode->outputImage();
    }

    public function auth() {
        $args = func_get_args();
        $method = carr::get($args, 0);
        $parameters = array_slice($args, 1);
    }
}
