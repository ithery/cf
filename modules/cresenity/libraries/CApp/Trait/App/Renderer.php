<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 27, 2019, 10:23:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_App_Renderer {

    protected $rendered = false;
    protected $viewData = null;

    public function renderContent($options = []) {
        $viewData = $this->getViewData();
        return carr::get($viewData, 'content');
    }

    public function renderStyles($options = []) {
        $viewData = $this->getViewData();
        return carr::get($viewData, 'head_client_script');
    }

    public function renderScripts($options = []) {
        $viewData = $this->getViewData();
        $endClientScript = carr::get($viewData, 'end_client_script', '');
        $readyClientScript = carr::get($viewData, 'ready_client_script', '');
        $loadClientScript = carr::get($viewData, 'load_client_script', '');
        $js = carr::get($viewData, 'js', '');
        $customJs = carr::get($viewData, 'custom_js', '');


        $cresJs = curl::base() . 'media/js/cres/dist/cres.js?v=' . md5(CFile::lastModified(DOCROOT . 'media/js/cres/dist/cres.js'));
        return <<<HTML
            ${endClientScript}
            <script src="${cresJs}"></script>
            <script>
                ${js}
                ${readyClientScript}
                if (window) {
                    window.onload = function () {
                        ${loadClientScript}
                    }
                }
                ${customJs}
            </script>
HTML;
    }

    public function renderTitle($options = []) {
        $viewData = $this->getViewData();
        return carr::get($viewData, 'title');
    }

    public function renderPageTitle($options = []) {
        $viewData = $this->getViewData();
        return carr::get($viewData, 'pageTitle');
    }

    public function getViewData() {
        if ($this->viewData == null) {
            $theme_path = '';

            $theme = CManager::theme()->getCurrentTheme();

            $themeFile = CF::getFile('themes', $theme);
            if (file_exists($themeFile)) {
                $themeData = include $themeFile;
                $theme_path = carr::get($themeData, 'theme_path');
                if ($theme_path == null) {
                    $theme_path = '';
                } else {
                    $theme_path .= '/';
                }
            }
            $viewData = array();
            $this->content = $this->element->html();
            $this->js = $this->element->js();

            $viewData['content'] = $this->content;
            $viewData['header_body'] = $this->header_body;
            $viewData['headerBody'] = $this->header_body;

            $viewData['title'] = $this->title;
            $viewData['pageTitle'] = $this->title;
            $asset = CManager::asset();

            $css_urls = $asset->getAllCssFileUrl();
            $js_urls = $asset->getAllJsFileUrl();
            $additional_js = "";
            if ($this->isUseRequireJs()) {

                foreach ($css_urls as $url) {
                    $additional_js .= "
                    $.cresenity._filesadded+='['+'" . $url . "'+']';
                    if(cresenity) {
                        cresenity.filesAdded+='['+'" . $url . "'+']';
                    }
                ";
                }
            }
            $js = "";

            $js .= PHP_EOL . $this->js . $additional_js;
            $jsScriptFile = '';
            
            if ($this->isUseRequireJs()) {
                
                $js = $asset->renderJsRequire($js);
            } else {
               
                $jsScriptFile = PHP_EOL.'<script>' . $asset->varJs() . '</script>';
                $jsScriptFile .= PHP_EOL.'<script>if(typeof define === "function") define=undefined;</script>';
                //$jsScriptFile .= '<script src="/media/js/capp.js?v='.uniqid().'"></script>';
                $jsScriptFile .= PHP_EOL.$asset->render(CManager_Asset::POS_END, CManager_Asset::TYPE_JS_FILE);
                
                $js = $asset->wrapJs($js, true);
            }





            if (ccfg::get("minify_js")) {
                $js = CJSMin::minify($js);
            }

            if (!$this->isUseRequireJs()) {
                $bar = CDebug::bar();
                if ($bar->isEnabled()) {
                    $js .= $bar->getJavascriptReplaceCode();
                }
            }

            $viewData['js'] = $js;

            $viewData['css_hash'] = "";
            $viewData['js_hash'] = "";
            if (ccfg::get("merge_css")) {
                $viewData['css_hash'] = $cs->create_css_hash();
            }
            if (ccfg::get("merge_js")) {
                $viewData['js_hash'] = $cs->create_js_hash();
            }


            $viewData['theme'] = $theme;
            $viewData['theme_path'] = $theme_path;
            $viewData['themePath'] = $theme_path;
            $viewData['head_client_script'] = $asset->render('head');
            $viewData['begin_client_script'] = $asset->render('begin');
            $viewData['end_client_script'] = $jsScriptFile;
            $viewData['load_client_script'] = $asset->render('load');
            $viewData['ready_client_script'] = $asset->render('ready');
            $viewData['custom_js'] = $this->custom_js;
            $viewData['custom_header'] = $this->custom_header;
            $viewData['custom_footer'] = $this->custom_footer;
            $viewData['show_breadcrumb'] = $this->showBreadcrumb;
            $viewData['showBreadcrumb'] = $this->showBreadcrumb;
            $viewData['show_title'] = $this->showTitle;
            $viewData['showTitle'] = $this->showTitle;
            $viewData['breadcrumb'] = $this->getBreadcrumb();
            $viewData['additional_head'] = $this->additional_head;
            $viewData['custom_data'] = $this->custom_data;
            $viewData['login_required'] = $this->loginRequired;
            $viewData['loginRequired'] = $this->loginRequired;
            $this->viewData = $viewData;
        }
        return $this->viewData;
    }

    public function allModuleData() {
        $allModule = CManager::asset()->module()->allModules();
        foreach ($allModule as $moduleName => $module) {
            foreach ($module as $type => $urls) {
                foreach ($urls as $indexUrl => $url) {
                    if ($type == 'js') {

                        $allModule[$moduleName][$type][$indexUrl] = CManager_Asset_Helper::urlJsFile($url);
                    }
                    if ($type == 'css') {
                        $allModule[$moduleName][$type][$indexUrl] = CManager_Asset_Helper::urlCssFile($url);
                    }
                }
            }
        }
    }

    public function rendered() {
        return $this->rendered;
    }

    /**
     * Render the html of this
     * 
     * @return void
     * @throws CException
     * @throws CApp_Exception
     */
    public function render() {

        if ($this->rendered) {
            throw new CException('CApp already Rendered' . cdbg::getTraceString());
        }
        $this->rendered = true;

        $this->registerCoreModules();

        CFEvent::run('CApp.beforeRender');

        if (c::request()->ajax()) {
            return $this->json();
        }


        $viewData = $this->getViewData();
        $v = $this->getView();
        $v->set($viewData);


        return $v->render();
    }

}
