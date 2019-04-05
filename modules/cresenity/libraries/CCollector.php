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

    public static function deprecated(Exception $exception) {
        $data = static::getDataFromException($exception);
        static::put(static::DEPRECATED, $data);
    }

    public static function exception(Exception $exception) {
        $data = static::getDataFromException($exception);
        static::put(static::EXCEPTION, $data);
    }

    public static function profiler() {
        static::put(static::PROFILER, $data);
    }

    private static function getDataFromException(Exception $exception) {
        $app = CApp::instance();

        $error = get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTrace();

        $data = [];
        $data['datetime'] = date('Y-m-d H:i:s');
        $data['appId'] = $app->appId();
        $data['appCode'] = $app->code();
        $data['admin'] = $app->admin();
        $data['member'] = $app->member();
        $data['user'] = $app->user();
        $data['role'] = $app->role();
        $data['org'] = $app->org();
        $data['orgId'] = $app->orgId();
        $data['error'] = $error;
        $data['message'] = $message;
        $data['file'] = $file;
        $data['line'] = $line;
        $data['trace'] = $trace;
        $data['browser'] = $this->getBrowser();
        $data['domain'] = CF::domain();
        $data['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['httpReferer'] = $_SERVER['HTTP_REFERER'];
        $data['remoteAddress'] = crequest::remote_address();
        $data['fullUrl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['CFVersion'] = CF_VERSION;

        return json_encode($data);
    }

    private function getBrowser() 
    { 
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
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
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
        
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
        
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    } 

}
