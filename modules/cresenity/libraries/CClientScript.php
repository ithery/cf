<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CClientScript extends CObject {

    use CTrait_Compat_ClientScript;

    protected static $_instance;

    /**
     * POS CONST
     */
    const POS_HEAD = CManager_Asset::POS_HEAD;
    const POS_BEGIN = CManager_Asset::POS_BEGIN;
    const POS_END = CManager_Asset::POS_END;
    const POS_READY = CManager_Asset::POS_READY;
    const POS_LOAD = CManager_Asset::POS_LOAD;

    /**
     * TYPE CONST
     */
    const TYPE_JS_FILE = CManager_Asset::TYPE_JS_FILE;
    const TYPE_JS = CManager_Asset::TYPE_JS;
    const TYPE_CSS_FILE = CManager_Asset::TYPE_CSS_FILE;
    const TYPE_CSS = CManager_Asset::TYPE_CSS;
    const TYPE_META = CManager_Asset::TYPE_META;
    const TYPE_LINK = CManager_Asset::TYPE_LINK;
    const TYPE_PLAIN = CManager_Asset::TYPE_PLAIN;

    /**
     * array of all type script
     * 
     * @var array
     */
    public static $allType = array(
        self::TYPE_JS_FILE,
        self::TYPE_JS,
        self::TYPE_CSS_FILE,
        self::TYPE_CSS,
        self::TYPE_META,
        self::TYPE_JS,
        self::TYPE_LINK,
        self::TYPE_PLAIN,
    );

    public static function allAvailablePos() {
        return array(self::POS_HEAD, self::POS_BEGIN, self::POS_END, self::POS_LOAD, self::POS_READY);
    }

    public static function allAvailableType() {
        return array(self::TYPE_JS_FILE, self::TYPE_JS, self::TYPE_CSS_FILE, self::TYPE_CSS, self::TYPE_META, self::TYPE_LINK, self::TYPE_PLAIN);
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

    public function registerJsFiles($files, $pos = "end") {
        return CManager::asset()->runTime()->registerJsFiles($files, $pos);
    }

    public function registerJsFile($file, $pos = "end") {
        return CManager::asset()->runTime()->registerJsFile($file, $pos);
    }

    public function unregisterJsFiles($files, $pos = null) {
        return CManager::asset()->runTime()->unregisterJsFiles($files, $pos);
    }

    public function unregisterJsFile($file, $pos = null) {
        return CManager::asset()->runTime()->unregisterJsFile($file, $pos);
    }

    public function registerCssFiles($files, $pos = "head") {
        return CManager::asset()->runTime()->registerCssFiles($files, $pos);
    }

    public function registerCssFile($file, $pos = "head") {
        return CManager::asset()->runTime()->registerCssFile($file, $pos);
    }

    public function unregisterCssFiles($files, $pos = null) {
        return CManager::asset()->runTime()->unregisterCssFiles($files, $pos);
    }

    public function unregisterCssFile($file, $pos = null) {
        return CManager::asset()->runTime()->unregisterCssFile($file, $pos);
    }

    public function jsFiles() {
        return CManager::asset()->runTime()->jsFiles();
    }

    public function registerJsInlines($jsArray, $pos = self::POS_HEAD) {
        return CManager::asset()->runTime()->registerJsInlines($jsArray, $pos);
    }

    public function registerJsInline($js, $pos = self::POS_HEAD) {
        return CManager::asset()->runTime()->registerJsInline($js, $pos);
    }

    public function registerCssInlines($cssArray, $pos = self::POS_HEAD) {
        return CManager::asset()->runTime()->registerCssInlines($cssArray, $pos);
    }

    public function registerCssInline($css, $pos = self::POS_HEAD) {
        return CManager::asset()->runTime()->registerCssInline($css, $pos);
    }

    public function registerPlains($plains, $pos = self::POS_HEAD) {
        return CManager::asset()->runTime()->registerPlains($plains, $pos);
    }

    public function registerPlain($plain, $pos = self::POS_HEAD) {
        return CManager::asset()->runTime()->registerPlain($plain, $pos);
    }

    public function create_js_hash() {
        return CResource::instance('js')->create_hash($this->jsFiles());
    }

    public function cssFiles() {
        $cssFileArray = array();
        foreach ($this->scripts as $script) {
            foreach ($script['css_file'] as $k) {
                $cssFileArray[] = $k;
            }
        }
        return $cssFileArray;
    }

    public function create_css_hash() {
        return CResource::instance('css')->create_hash($this->cssFiles());
    }

    public function js($hash) {
        return CResource::instance('js')->load($hash);
    }

    public function css($hash) {
        return CResource::instance('css')->load($hash);
    }

    public function manifest() {
        $jsFiles = $this->jsFiles();
        $cssFiles = $this->cssFiles();
        $manifest = array();
        $manifest["files"] = array();
        $lastFilemtime = 0;
        foreach ($jsFiles as $f) {
            $urlJsFile = $this->urlJsFile($f);
            $fullpathJsFile = $f;
            $arr = array();
            $arr["type"] = "js";
            $arr["url"] = $urlJsFile;
            $arr["file"] = $f;
            $filemtime = filemtime($fullpathJsFile);
            $arr["version"] = $filemtime;
            if ($lastFilemtime < $filemtime) {
                $lastFilemtime = $filemtime;
            }
            $manifest["files"][] = $arr;
        }
        foreach ($cssFiles as $f) {
            $urlCssFile = $this->urlCssFile($f);
            $fullpathCssFile = $f;
            $arr = array();
            $arr["type"] = "css";
            $arr["url"] = $urlCssFile;
            $arr["file"] = $f;

            $file = explode('?', $fullpathCssFile);
            $fullpathCssFile = $file[0];


            $filemtime = filemtime($fullpathCssFile);
            $arr["version"] = $filemtime;
            if ($lastFilemtime < $filemtime)
                $lastFilemtime = $filemtime;
            $manifest["files"][] = $arr;
        }
        $manifest["version"] = $lastFilemtime;
        return $manifest;
    }

    public function renderJsRequire($js) {
        return CManager::asset()->renderJsRequire($js);
    }

    public function render($pos, $type = null) {
        return CManager::asset()->render($pos, $type);
    }

}
