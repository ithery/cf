<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CClientScript extends CObject {

    use CTrait_Compat_ClientScript;

    protected $scripts;
    protected static $_instance;

    /**
     * POS CONST
     */
    const POS_HEAD = 'head';
    const POS_BEGIN = 'begin';
    const POS_END = 'end';
    const POS_READY = 'ready';
    const POS_LOAD = 'load';

    /**
     * TYPE CONST
     */
    const TYPE_JS_FILE = 'js_file';
    const TYPE_JS = 'js';
    const TYPE_CSS_FILE = 'css_file';
    const TYPE_CSS = 'css';
    const TYPE_META = 'meta';
    const TYPE_LINK = 'link';

    public function __construct() {
        $this->reset();
    }

    public static function allAvailablePos() {
        return array(self::POS_HEAD, self::POS_BEGIN, self::POS_END, self::POS_LOAD, self::POS_READY);
    }

    public static function allAvailableType() {
        return array(self::TYPE_JS_FILE, self::TYPE_JS, self::TYPE_CSS_FILE, self::TYPE_CSS, self::TYPE_META, self::TYPE_LINK);
    }

    /**
     * 
     * @return CClientScript
     */
    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new CClientScript();
        }
        return self::$_instance;
    }

    public function reset() {
        $ALLPOS = self::allAvailablePos();
        $ALLTYPE = self::allAvailableType();
        $this->scripts = array();
        foreach ($ALLPOS as $pos) {
            $this->scripts[$pos] = array();
            foreach ($ALLTYPE as $type) {
                $this->scripts[$pos][$type] = array();
            }
        }
    }

    public function fullpath_js_file($file) {
        $dirs = CF::get_dirs('media');

        foreach ($dirs as $dir) {
            $path = $dir . 'js' . DS . $file;


            if (file_exists($path)) {
                return $path;
            }
        }

        $path = DOCROOT . "media" . DS . 'js' . DS;
        return $path . $file;
    }

    public function fullpath_css_file($file) {
        $dirs = CF::get_dirs('media');

        foreach ($dirs as $dir) {
            $path = $dir . 'css' . DS . $file;

            if (file_exists($path)) {
                return $path;
            }
        }
        $path = DOCROOT . "media" . DS . 'css' . DS;
        return $path . $file;
    }

    public function url_js_file($file = null) {
        if ($file == null) {
            $files = $this->js_files();
            $urls = array();
            foreach ($files as $f) {
                $urls[] = $this->url_js_file($f);
            }
            return $urls;
        }
        //return CResource::instance('js')->url($file);
        //$path = curl::base()."media/js/";
        $docroot = str_replace(DS, "/", DOCROOT);
        $file = str_replace(DS, "/", $file);
        $base_url = curl::base();
        if (CApp::instance()->is_mobile()) {

            $base_url = curl::base(false, 'http');
        }

//        if(CF::domain()=='livemall.co.id') {
//            $base_url = 'http://livemall-70ae.kxcdn.com/';
//        }

        $file = str_replace($docroot, $base_url, $file);

        return $file;
    }

    public function url_css_file($file = null) {
        if ($file == null) {
            $files = $this->css_files();

            $urls = array();
            foreach ($files as $f) {
                $urls[] = $this->url_css_file($f);
            }
            return $urls;
        }
        //return CResource::instance('css')->url($file);
        $docroot = str_replace(DS, "/", DOCROOT);
        $file = str_replace(DS, "/", $file);
        $base_url = curl::base();
        if (CApp::instance()->is_mobile()) {

            $base_url = curl::base(false, 'http');
        }
        $file = str_replace($docroot, $base_url, $file);

        return $file;
    }

    public function registerJsFiles($files, $pos = "end") {
        if (!is_array($files)) {
            $files = array($files);
        }
        foreach ($files as $file) {
            $this->registerJsFile($file, $pos);
        }
    }

    public function registerCssFiles($files, $pos = "head") {
        if (!is_array($files)) {
            $files = array($files);
        }
        foreach ($files as $file) {
            $this->registerCssFile($file, $pos);
        }
    }

    public function unregisterJsFiles($files, $pos = null) {
        if (!is_array($files)) {
            $files = array($files);
        }
        foreach ($files as $file) {
            $this->unregisterJsFile($file, $pos);
        }
    }

    public function unregisterJsFile($file, $pos = null) {
        //we will locate all pos for this pos if pos =null;
        if ($pos == null) {
            $pos = self::allAvailablePos();
        }
        if (!is_array($pos)) {
            $pos = array($pos);
        }
        foreach ($pos as $p) {
            $jsFiles = &$this->scripts[$p]['js_file'];
            foreach ($jsFiles as $k => $jsFile) {
                if ($jsFile == $file) {
                    unset($jsFiles[$k]);
                }
            }
        }
    }

    public function registerJsFile($file, $pos = "end") {
        $dir_file = $file;
        $js_version = '';
        if (strpos($file, '?') !== false) {
            $dir_file = substr($file, 0, strpos($file, '?'));
            $js_version = substr($file, strpos($file, '?'), strlen($file) - 1);
        }
        $js_file = $this->fullpath_js_file($dir_file);
        if (strpos($dir_file, 'http') !== false) {
            $js_file = $dir_file;
            // do nothing
        } else {
            $js_file = $this->fullpath_js_file($dir_file);
            if (!file_exists($js_file)) {
                trigger_error('JS File not exists, ' . $file);
            }
            if (strlen($js_version) > 0) {
                $js_file .= $js_version;
            }
        }
        $this->scripts[$pos]['js_file'][] = $js_file;
        return $this;
    }

    
    public function unregisterCssFiles($files, $pos = null) {
        if (!is_array($files)) {
            $files = array($files);
        }
        foreach ($files as $file) {
            $this->unregisterCssFile($file, $pos);
        }
    }

    public function unregisterCssFile($file, $pos = null) {
        //we will locate all pos for this pos if pos =null;
        if ($pos == null) {
            $pos = self::allAvailablePos();
        }
        if (!is_array($pos)) {
            $pos = array($pos);
        }
        foreach ($pos as $p) {
            $cssFiles = &$this->scripts[$p]['css_file'];
            foreach ($cssFiles as $k => $cssFile) {
                if ($cssFile == $file) {
                    unset($cssFiles[$k]);
                }
            }
        }
    }
    
    public function registerCssFile($file, $pos = "head") {
        $dir_file = $file;
        $css_version = '';
        if (strpos($file, '?') !== false) {
            $dir_file = substr($file, 0, strpos($file, '?'));
            $css_version = substr($file, strpos($file, '?'), strlen($file) - 1);
        }
        if (strpos($dir_file, 'http') !== false) {
            $css_file = $dir_file;
            // do nothing
        } else {
            $css_file = $this->fullpath_css_file($dir_file);
            if (!file_exists($css_file)) {
                trigger_error('CSS File not exists, ' . $file);
            }
            if (strlen($css_version) > 0) {
                $css_file .= $css_version;
            }
        }
        $this->scripts[$pos]['css_file'][] = $css_file;
        return $this;
    }

    public function js_files() {
        $js_file_array = array();
        foreach ($this->scripts as $sc) {

            foreach ($sc['js_file'] as $k) {
                $js_file_array[] = $k;
            }
        }
        return $js_file_array;
    }

    public function create_js_hash() {
        return CResource::instance('js')->create_hash($this->js_files());
    }

    public function css_files() {
        $css_file_array = array();
        foreach ($this->scripts as $sc) {

            foreach ($sc['css_file'] as $k) {
                $css_file_array[] = $k;
            }
        }
        return $css_file_array;
    }

    public function create_css_hash() {
        return CResource::instance('css')->create_hash($this->css_files());
    }

    public function js($hash) {
        return CResource::instance('js')->load($hash);
    }

    public function css($hash) {
        return CResource::instance('css')->load($hash);
    }

    public function manifest() {
        $js_files = $this->js_files();
        $css_files = $this->css_files();
        $manifest = array();
        $manifest["files"] = array();
        $last_filemtime = 0;
        foreach ($js_files as $f) {
            $url_js_file = $this->url_js_file($f);
            $fullpath_js_file = $f;
            $arr = array();
            $arr["type"] = "js";
            $arr["url"] = $url_js_file;
            $arr["file"] = $f;
            $filemtime = filemtime($fullpath_js_file);
            $arr["version"] = $filemtime;
            if ($last_filemtime < $filemtime)
                $last_filemtime = $filemtime;
            $manifest["files"][] = $arr;
        }
        foreach ($css_files as $f) {
            $url_css_file = $this->url_css_file($f);
            $fullpath_css_file = $f;
            $arr = array();
            $arr["type"] = "css";
            $arr["url"] = $url_css_file;
            $arr["file"] = $f;

            $file = explode('?', $fullpath_css_file);
            $fullpath_css_file = $file[0];


            $filemtime = filemtime($fullpath_css_file);
            $arr["version"] = $filemtime;
            if ($last_filemtime < $filemtime)
                $last_filemtime = $filemtime;
            $manifest["files"][] = $arr;
        }
        $manifest["version"] = $last_filemtime;
        return $manifest;
    }

    public function render_js_require($js) {
        //return CClientModules::instance()->require_js($js);
        $app = CApp::instance();
        $js_files = $this->js_files();
        $js_open = "";
        $js_close = "";
        $js_before = "";
        $i = 0;
        $man = CManager::instance();
        foreach ($js_files as $f) {
            $url_js_file = $this->url_js_file($f);
            if ($man->is_mobile()) {
                $mobile_path = $man->get_mobile_path();
                if (strlen($mobile_path) > 0) {
                    $url_js_file = $mobile_path . $f;
                }
            }


            $js_open .= str_repeat("\t", $i) . "require(['" . $url_js_file . "'],function(){" . PHP_EOL;

            $js_close .= "})";
            $i++;
        }

        $js .= "
                if (typeof capp_started_event_initialized === 'undefined') {
                    capp_started_event_initialized=false;
                 }
                if(!capp_started_event_initialized) {
                    var evt = document.createEvent('Events');
                    evt.initEvent( 'capp-started', false, true, window, 0);
                    capp_started_event_initialized=true;
                    document.dispatchEvent(evt);
                }


            ";


        $js_before .= "
            window.capp = " . json_encode(CApp::variables()) . ";
            ";

        return $js_before . $js_open . $js . PHP_EOL . $js_close . ";" . PHP_EOL;
    }

    public function render($pos, $type = array("js_file", "css_file", "js", "css", "meta", "link")) {
        $script = "";
        $app = CApp::instance();
        $man = CManager::instance();
        if (!is_array($type))
            $type = array($type);
        foreach ($this->scripts[$pos] as $k => $v) {
            if (in_array($k, $type)) {
                foreach ($v as $s) {
                    switch ($k) {
                        case "js_file":
                            if (!ccfg::get('merge_js')) {
                                $url_js_file = $this->url_js_file($s);
                                if ($man->is_mobile()) {
                                    $mobile_path = $man->get_mobile_path();
                                    if (strlen($mobile_path) > 0) {
                                        $url_js_file = $mobile_path . $s;
                                    }
                                }

                                $script .= '<script src="' . $url_js_file . '"></script>' . PHP_EOL;
                            }
                            break;
                    }
                    switch ($k) {
                        case "css_file":
                            if (!ccfg::get('merge_css')) {
                                $url_css_file = $this->url_css_file($s);
                                if ($man->is_mobile()) {
                                    $mobile_path = $man->get_mobile_path();
                                    if (strlen($mobile_path) > 0) {
                                        $url_css_file = $mobile_path . $s;
                                    }
                                }

                                $script .= '<link href="' . $url_css_file . '" rel="stylesheet" />' . PHP_EOL;
                            }
                            break;
                    }
                }
            }
        }

        return $script;
    }

}
