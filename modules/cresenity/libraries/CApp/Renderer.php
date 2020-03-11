<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 7, 2018, 7:10:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Renderer {

    use CTrait_HasOptions;

    protected $app;
    protected $rendered = false;

    public function getConfig($name) {
        $config = CF::config('renderer.'. $name);
      
        return $config;
    }

    public function __construct($app, $options = null) {
        if ($options == null) {
            $configName = CF::config('renderer.default');
            $options = $this->getConfig($configName);
        }
        $this->rendered=false;

        $this->setOptions($options);
        $this->app = $app;
    }

    public function render() {
        if ($this->rendered) {
            throw new CException('CApp already rendered');
        }
        $this->rendered = true;

        $app->registerCoreModules();


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



        return $v->render();
    }

}
