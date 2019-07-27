<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 27, 2019, 10:23:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_App_Renderer {

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

        $js = $asset->renderJsRequire($js);

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
        $viewData['end_client_script'] = "";
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
