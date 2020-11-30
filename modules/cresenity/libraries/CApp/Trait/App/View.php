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
        if (!($view instanceof CView_View)) {
            $view = CView::factory($view);
        }
        $this->view = $view;
        $this->viewName = $view->getName();

        if ($this->isUsingBlade()) {

            $this->useRequireJs = false;
        }
    }

    public function getView() {
        if (!$this->isUserLogin() && $this->config("have_user_login") && $this->loginRequired) {
            $view = $this->viewLoginName;
            if (!($view instanceof CView_View)) {
                $view = CView::factory($view);
            }
            $this->view = $view;
            $this->viewName = $view->getName();
        }

        if ($this->view == null) {
            $viewName = $this->viewName;

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
        $this->setView($viewName);
    }

    public function setViewLoginName($viewLoginName) {
        $this->viewLoginName = $viewLoginName;
    }

    public function isUsingBlade() {
        if ($view = $this->getView()) {


            if ($view instanceof CView_View) {

                if ($view->getEngine() instanceof CView_Engine_CompilerEngine) {
                    return true;
                }
            }
        }
        return false;
    }

}
