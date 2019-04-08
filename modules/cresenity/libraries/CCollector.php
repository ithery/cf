<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * 
 */
class CCollector {

    const EXT = '.txt';
    const DEPRECATED = 'deprecated';
    const EXCEPTION = 'exception';
    const PROFILER = 'profiler';
    const TYPE = ['deprecated', 'exception', 'profiler'];

    public static function getDirectory() {
        $path = DOCROOT . 'temp' . DS;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $path .= 'collector' . DS;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    public static function get($type = null) {
        $path = static::getDirectory();
        $data = [];

        if ($type && strtolower($type) != 'all') {
            if (!in_array($type, static::TYPE)) {
                throw new CException("Type $type is not found");
            }

            $tempPath = $path . DS . $type . DS;
            foreach (glob($tempPath . '*' . static::EXT) as $file) {
                $data = static::getContent($file);
            }
        } else {
            foreach (static::TYPE as $type) {
                $tempPath = $path . DS . $type . DS;
                $data[$type] = [];
                foreach (glob($tempPath . '*' . static::EXT) as $file) {
                    $data[$type] = static::getContent($file);
                }
            }
        }

        return $data;
    }

    private static function getContent($path) {
        $data = [];
        if (file_exists($path)) {
            $content = file($pathinfo);
            $data = array_map(function($data) {
                return json_decode($data);
            }, $content);
        }
        return $data;
    }

    public static function put($type, $data) {
        if (!in_array($type, static::TYPE)) {
            throw new CException("Type $type is not found");
        }

        if (!is_string($data)) {
            $data = json_encode($data);
        }

        json_decode($data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CException(json_last_error_msg());
        }

        $path = static::getDirectory();
        $path .= $type . DS;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $path .= date('Ymd') . static::EXT;
        file_put_contents($path, $data . PHP_EOL, FILE_APPEND | LOCK_EX);

        return true;
    }

    public static function deprecated() {
        $configCollector = CConfig::instance('collector');

        if ($configCollector->get('deprecated')) {
            static $totalDeprecated = 0;
            $totalDeprecated++;

            if ($totalDeprecated < 10) {
                try {
                    throw new Exception('Deprecated');
                } catch (Exception $ex) {
                    $data = static::getDataFromException($ex, $isDeprecated = true);
                    static::put(static::DEPRECATED, $data);
                    unset($ex);
                }
            }
        }
    }

    public static function exception(Exception $exception) {
        $data = static::getDataFromException($exception);
        static::put(static::EXCEPTION, $data);
    }

    public static function profiler() {
        static::put(static::PROFILER, $data);
    }

    private static function getDataFromException(Exception $exception, $isDeprecated = false) {
        $app = CApp::instance();

        // Start validation of the controller
        $controllerClass = str_replace('/', '_', CFRouter::$controller_dir_ucfirst);
        $controllerClass = 'Controller_' . $controllerClass . ucfirst(CFRouter::$controller);
        $error = get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTrace();
        if ($isDeprecated) {

            $stack1 = carr::get($trace, 1);
            $stack2 = carr::get($trace, 2);

            $func1 = isset($stack1['class']) ? $stack1['class'] . "::" : "";
            $func1 .= carr::get($stack1, 'function');
            $func1 .= isset($stack1['file']) ? ' at file' . $stack1['file'] : "";
            $func1 .= isset($stack1['line']) ? '[' . $stack1['line'] . ']' : "";

            $func2 = isset($stack2['class']) ? $stack2['class'] . "::" : "";
            $func2 .= carr::get($stack2, 'function');
            $func2 .= isset($stack2['file']) ? ' at file' . $stack2['file'] : "";
            $func2 .= isset($stack2['line']) ? '[' . $stack2['line'] . ']' : "";

            $message = "Deprecated:" . $func1 . " called in " . $func2;
        }
        $browser = new CBrowser();
        $data = [];
        $rawPost = file_get_contents('php://input');
        $data['datetime'] = date('Y-m-d H:i:s');
        $data['appId'] = $app->appId();
        $data['appCode'] = $app->code();
        $data['user'] = CApp_Base::username();
        $data['role'] = CApp_Base::roleName();
        $data['orgId'] = CApp_Base::orgId();
        $data['orgCode'] = CApp_Base::orgCode();
        $data['error'] = $error;
        $data['message'] = $message;
        $data['file'] = $file;
        $data['line'] = $line;
        $data['trace'] = json_encode($trace);
        $data['browser'] = $browser->getBrowser();
        $data['browserVersion'] = $browser->getVersion();
        $data['platform'] = $browser->getPlatform();
        $data['domain'] = CF::domain();
        $data['controller'] = $controllerClass;
        $data['method'] = CFRouter::$method;
        $data['userAgent'] = carr::get($_SERVER, 'HTTP_USER_AGENT');
        $data['httpReferer'] = carr::get($_SERVER, 'HTTP_REFERER');
        $data['remoteAddress'] = CApp_Base::remoteAddress();
        $data['fullUrl'] = curl::current();
        $data['protocol'] = CApp_Base::protocol();
        $data['CFVersion'] = CF_VERSION;
        $data['postData'] = $rawPost;
        $data['fileData'] = json_encode($_FILES);

        return json_encode($data);
    }

    private function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

}
