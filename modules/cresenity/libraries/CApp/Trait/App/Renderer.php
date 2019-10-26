<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 27, 2019, 10:23:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_App_Renderer {

    public static function variables() {
        $variables = array();
        $variables['decimal_separator'] = ccfg::get('decimal_separator') === null ? '.' : ccfg::get('decimal_separator');
        $variables['decimalSeparator'] = ccfg::get('decimal_separator') === null ? '.' : ccfg::get('decimal_separator');
        $variables['thousand_separator'] = ccfg::get('thousand_separator') === null ? ',' : ccfg::get('thousand_separator');
        $variables['thousandSeparator'] = ccfg::get('thousand_separator') === null ? ',' : ccfg::get('thousand_separator');
        $variables['decimal_digit'] = ccfg::get('decimal_digit') === null ? '0' : ccfg::get('decimal_digit');
        $variables['decimalDigit'] = ccfg::get('decimal_digit') === null ? '0' : ccfg::get('decimal_digit');
        $variables['have_clock'] = ccfg::get('have_clock') === null ? false : ccfg::get('have_clock');
        $variables['haveClock'] = ccfg::get('have_clock') === null ? false : ccfg::get('have_clock');
        $variables['have_scroll_to_top'] = ccfg::get('have_scroll_to_top') === null ? true : ccfg::get('have_scroll_to_top');
        $variables['haveScrollToTop'] = ccfg::get('have_scroll_to_top') === null ? true : ccfg::get('have_scroll_to_top');


        $bootstrap = ccfg::get('bootstrap');
        $themeData = CManager::instance()->getThemeData();
        if (isset($themeData) && strlen(carr::get($themeData, 'bootstrap')) > 0) {
            $bootstrap = carr::get($themeData, 'bootstrap');
        }

        if (strlen($bootstrap) == 0) {
            $bootstrap = '2.3';
        }
        $variables['bootstrap'] = $bootstrap;

        $variables['base_url'] = curl::base();
        $variables['baseUrl'] = curl::base();
        $variables['label_confirm'] = clang::__("Are you sure ?");
        $variables['labelConfirm'] = clang::__("Are you sure ?");
        $variables['label_ok'] = clang::__("OK");
        $variables['labelOk'] = clang::__("OK");
        $variables['label_cancel'] = clang::__("Cancel");
        $variables['labelCancel'] = clang::__("Cancel");

        $asset = CManager::asset();
        $variables['requireJs'] = $asset->isUseRequireJs();
        if (!$asset->isUseRequireJs()) {


            $variables['requireJs'] = ccfg::get('require_js');


            //we collect all client modules data
            $allModules = CManager::asset()->module()->allModules();
            $variables['modules'] = $allModules;
            $variables['theme'] = array();
            $variables['theme']['name'] = CManager::theme()->getCurrentTheme();
            $variables['theme']['data'] = CManager::theme()->getThemeData();

            $variables['assets'] = CManager::asset()->getAllJsFileUrl();
        }
        return $variables;
    }

    public function getVariables() {
        return $this->variables();
    }

    public function getViewData() {
        $theme_path = '';

        $theme = CManager::theme()->getCurrentTheme();

        $themeFile = CF::get_file('themes', $theme);
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
        $this->content = parent::html();
        $this->js = parent::js();

        $viewData['content'] = $this->content;
        $viewData['header_body'] = $this->header_body;
        $viewData['headerBody'] = $this->header_body;

        $viewData['title'] = $this->title;
        $asset = CManager::asset();

        $css_urls = $asset->getAllCssFileUrl();
        $js_urls = $asset->getAllJsFileUrl();
        $additional_js = "";
        foreach ($css_urls as $url) {
            $additional_js .= "
					$.cresenity._filesadded+='['+'" . $url . "'+']';
					if(cresenity) {
                                            cresenity.filesAdded+='['+'" . $url . "'+']';
                                        }
				";
        }
        $js = "";

        $js .= PHP_EOL . $this->js . $additional_js;
        $jsScriptFile = '';

        if (ccfg::get("require_js")) {
            $js = $asset->renderJsRequire($js);
        } else {
            $jsScriptFile .= '<script>' . $asset->varJs() . '</script>';
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

}
