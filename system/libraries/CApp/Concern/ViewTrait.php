<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CApp_Concern_ViewTrait {
    protected static $viewCallback;

    private $viewName = 'cresenity/capp/page';

    private $viewLoginName = 'cresenity/capp/login';

    /**
     * View.
     *
     * @var CView_View}string
     */
    private $view;

    public function setView($view) {
        if (!($view instanceof CView_View)) {
            $view = CView::factory($view);
        }
        $this->view = $view;
        $this->viewName = $view->getName();

        if ($this->isUsingBlade()) {
            $this->useRequireJs = false;
        }

        return $this;
    }

    /**
     * @return CView_View
     */
    public function getView() {
        /** @var CApp $this */
        if (!$this->isUserLogin() && $this->isAuthEnabled()) {
            $view = $this->viewLoginName;

            if (!($view instanceof CView_View)) {
                $view = CView::factory($view);
            }
            $this->view = $view;
            $this->viewName = $view->getName();
        }

        if ($this->view == null) {
            $viewName = $this->viewName;

            if (static::$viewCallback != null && is_callable(static::$viewCallback)) {
                $callback = static::$viewCallback;
                $viewName = $callback($viewName);
            }
            $v = null;

            if ($v == null) {
                if (!CView::exists($viewName)) {
                    throw new CApp_Exception(c::__('view :viewName not exists', ['viewName' => $viewName]));
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

        return $this;
    }

    public function setViewLoginName($viewLoginName) {
        $this->viewLoginName = $viewLoginName;

        return $this;
    }

    public function isUsingBlade() {
        /** @var CApp $this */
        if (!$this->isUserLogin() && $this->config('have_user_login') && $this->isAuthEnabled()) {
            return false;
        }
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
