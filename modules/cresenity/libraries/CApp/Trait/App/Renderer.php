<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 27, 2019, 10:23:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_App_Renderer {

    protected $rendered = false;

    public function getViewData() {
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
        $asset = CManager::asset();

        $css_urls = $asset->getAllCssFileUrl();
        $js_urls = $asset->getAllJsFileUrl();
        $additional_js = "";
        if ($asset->isUseRequireJs()) {

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

        if ($asset->isUseRequireJs()) {
            $js = $asset->renderJsRequire($js);
        } else {
            $jsScriptFile .= '<script>' . $asset->varJs() . '</script>';
            $jsScriptFile .= '<script>if(typeof define === "function") define=undefined;</script>';
            //$jsScriptFile .= '<script src="/media/js/capp.js?v='.uniqid().'"></script>';
            $jsScriptFile .= $asset->render(CManager_Asset::POS_END, CManager_Asset::TYPE_JS_FILE);
            $js = $asset->wrapJs($js, true);
        }





        if (ccfg::get("minify_js")) {
            $js = CJSMin::minify($js);
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

        return $viewData;
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


        CFBenchmark::start('CApp Render');

        CFEvent::run('CApp.beforeRender');

        if (crequest::is_ajax()) {
            return $this->json();
        }
        $v = null;

        $viewName = $this->viewName;
        if (ccfg::get("install")) {
            $viewName = 'cinstall/page';
        } else if ($this->signup) {
            $viewName = 'ccore/signup';
        } else if ($this->resend) {
            $viewName = 'ccore/resend_activation';
        } else if ($this->activation) {
            $viewName = 'ccore/activation';
        } else if (!$this->isUserLogin() && $this->config("have_user_login") && $this->loginRequired) {
            $viewName = $this->viewLoginName;
        } else if (!$this->isUserLogin() && $this->config("have_static_login") && $this->loginRequired) {
            $viewName = 'ccore/static_login';
        }

        if (self::$viewCallback != null && is_callable(self::$viewCallback)) {
            $viewName = self::$viewCallback($viewName);
        }

        $themePath = CManager::theme()->getThemePath();

        if (CView::exists($themePath . $viewName)) {
            $v = CView::factory($themePath . $viewName);
        }
        if ($v == null) {
            if (!CView::exists($viewName)) {
                throw new CApp_Exception('view :viewName not exists', array(':viewName' => $viewName));
            }
            $v = CView::factory($viewName);
        }


        $viewData = $this->getViewData();
        $v->set($viewData);

        $result = CFBenchmark::stop('CApp Render');


        return $v->render();
    }

}
