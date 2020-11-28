<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
trait CApp_Trait_App_View {

    private $viewName = 'cpage';
    private $view;
    private $viewLoginName = 'ccore/login';
    protected static $viewCallback;

    
    public function setView($view) {
        if(!($view instanceof CView_View)) {
            $view = CView::factory($view);
        }
        $this->view = $view;
        $this->viewName = $view->getName();
    }
    
    public function getView() {
        if ($this->view == null) {
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
            $v = null;



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
            $this->view = $v;
        }
        return $this->view;
    }

    public function setViewCallback(callable $viewCallback) {
        self::$viewCallback = $viewCallback;
    }

    public function setViewName($viewName) {
        $this->viewName = $viewName;
    }

    public function setViewLoginName($viewLoginName) {
        $this->viewLoginName = $viewLoginName;
    }

}
