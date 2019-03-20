<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 1:13:38 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_Asset {

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
    const TYPE_PLAIN = 'plain';

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

    /**
     *
     * @var CManager_Asset_Container_Theme
     */
    protected $themeContainer;

    /**
     *
     * @var CManager_Asset_Container_RunTime
     */
    protected $runTimeContainer;

    /**
     *
     * @var CManager_Asset_Module
     */
    protected $module;

    public function __construct() {
        $this->runTimeContainer = new CManager_Asset_Container_RunTime();
        $this->themeContainer = new CManager_Asset_Container_Theme();
        $this->module = new CManager_Asset_Module();
    }

    public function reset() {
        $this->runTimeContainer->reset();
        $this->themeContainer->reset();
        $this->module->reset();
    }

    public static function allAvailablePos() {
        return array(self::POS_HEAD, self::POS_BEGIN, self::POS_END, self::POS_LOAD, self::POS_READY);
    }

    public static function allAvailableType() {
        return array(self::TYPE_JS_FILE, self::TYPE_JS, self::TYPE_CSS_FILE, self::TYPE_CSS, self::TYPE_META, self::TYPE_LINK);
    }

    /**
     * 
     * @return CManager_Asset_Container_RunTime
     */
    public function runTime() {
        return $this->runTimeContainer;
    }

    /**
     * 
     * @return CManager_Asset_Container_Theme
     */
    public function theme() {
        return $this->themeContainer;
    }

    /**
     * 
     * @return CManager_Asset_Module
     */
    public function module() {
        return $this->module;
    }

    public function getAllCssFileUrl() {
        $themeCss = $this->themeContainer->getAllCssFileUrl();
        $runTimeCss = $this->runTimeContainer->getAllCssFileUrl();
        $moduleThemeCss = $this->module->getThemeContainer()->getAllCssFileUrl();
        $moduleRunTimeCss = $this->module->getRunTimeContainer()->getAllCssFileUrl();
        return array_merge($moduleThemeCss, $themeCss, $moduleRunTimeCss, $runTimeCss);
    }

    public function getAllJsFileUrl() {
        $themeJs = $this->themeContainer->getAllJsFileUrl();
        $runTimeJs = $this->runTimeContainer->getAllJsFileUrl();
        $moduleThemeJs = $this->module->getThemeContainer()->getAllJsFileUrl();
        $moduleRunTimeJs = $this->module->getRunTimeContainer()->getAllJsFileUrl();
        return array_merge($moduleThemeJs, $themeJs, $moduleRunTimeJs, $runTimeJs);
    }

    public function renderJsRequire($js) {
        //return CClientModules::instance()->require_js($js);
        $app = CApp::instance();


        $moduleThemejsFiles = $this->module->getThemeContainer()->jsFiles();
        $themejsFiles = $this->themeContainer->jsFiles();
        $moduleRunTimejsFiles = $this->module->getRunTimeContainer()->jsFiles();
        $runTimejsFiles = $this->runTimeContainer->jsFiles();



        $jsFiles = array_merge($moduleThemejsFiles, $themejsFiles, $moduleRunTimejsFiles, $runTimejsFiles);

        $js_open = "";
        $js_close = "";
        $js_before = "";
        $i = 0;
        $manager = CManager::instance();
        if ($manager->getUseRequireJs()) {
            foreach ($jsFiles as $f) {
                $urlJsFile = CManager_Asset_Helper::urlJsFile($f);
                if ($manager->isMobile()) {
                    $mobilePath = $manager->getMobilePath();
                    if (strlen($mobilePath) > 0) {
                        $urlJsFile = $mobilePath . $f;
                    }
                }


                $js_open .= str_repeat("\t", $i) . "require(['" . $urlJsFile . "'],function(){" . PHP_EOL;

                $js_close .= "})";
                $i++;
            }
        }
        $js .= "
            if (typeof cappStartedEventInitilized === 'undefined') {
                cappStartedEventInitilized=false;
             }
            if(!cappStartedEventInitilized) {
                var evt = document.createEvent('Events');
                evt.initEvent('capp-started', false, true, window, 0);
                cappStartedEventInitilized=true;
                document.dispatchEvent(evt);
            }


        ";


        $js_before .= "
            window.capp = " . json_encode(CApp::variables()) . ";
            ";

        $js .= CJavascript::compile();
        $bar = CDebug::bar();
        if ($bar->isEnabled()) {
            $js .= $bar->getJavascriptReplaceCode();
        }


        return $js_before . $js_open . $js . PHP_EOL . $js_close . ";" . PHP_EOL;
    }

    public function render($pos, $type = null) {
        $moduleThemeScripts = $this->module->getThemeContainer()->getScripts($pos);
        $themeScripts = $this->themeContainer->getScripts($pos);
        $moduleRunTimeScripts = $this->module->getRunTimeContainer()->getScripts($pos);
        $runTimeScripts = $this->runTimeContainer->getScripts($pos);
        $scriptArray = array();
        $scriptArray = carr::merge($scriptArray, $moduleThemeScripts);
        $scriptArray = carr::merge($scriptArray, $themeScripts);
        $scriptArray = carr::merge($scriptArray, $moduleRunTimeScripts);
        $scriptArray = carr::merge($scriptArray, $runTimeScripts);

        $script = '';
        $manager = CManager::instance();
        if ($type == null) {
            $type = self::$allType;
        }
        if (!is_array($type)) {
            $type = array($type);
        }
        foreach ($scriptArray as $scriptType => $scriptValueArray) {
            if (in_array($scriptType, $type)) {
                foreach ($scriptValueArray as $scriptValue) {
                    switch ($scriptType) {
                        case self::TYPE_JS_FILE:
                            if (!ccfg::get('merge_js')) {
                                $urlJsFile = CManager_Asset_Helper::urlJsFile($scriptValue);
                                if ($manager->is_mobile()) {
                                    $mobilePath = $manager->getMobilePath();
                                    if (strlen($mobilePath) > 0) {
                                        $urlJsFile = $mobilePath . $scriptValue;
                                    }
                                }

                                $script .= '<script src="' . $urlJsFile . '"></script>' . PHP_EOL;
                            }
                            break;
                        case self::TYPE_CSS_FILE:
                            if (!ccfg::get('merge_css')) {
                                $urlCssFile = CManager_Asset_Helper::urlCssFile($scriptValue);
                                if ($manager->is_mobile()) {
                                    $mobilePath = $manager->getMobilePath();
                                    if (strlen($mobilePath) > 0) {
                                        $urlCssFile = $mobilePath . $scriptValue;
                                    }
                                }

                                $script .= '<link href="' . $urlCssFile . '" rel="stylesheet" />' . PHP_EOL;
                            }
                            break;

                        case self::TYPE_JS:
                            if (!ccfg::get('merge_js')) {

                                $script .= '<script>' . $scriptValue . '</script>' . PHP_EOL;
                            }
                            break;
                        case self::TYPE_CSS:
                            if (!ccfg::get('merge_css')) {

                                $script .= '<style>' . $scriptValue . '</style>' . PHP_EOL;
                            }
                            break;
                        case self::TYPE_PLAIN:

                            $script .= $scriptValue . PHP_EOL;
                            break;
                    }
                }
            }
        }

        return $script;
    }

}
