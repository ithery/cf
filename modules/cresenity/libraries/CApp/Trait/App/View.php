<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
trait CApp_Trait_App_View {
    private $viewName = 'capp/page';

    private $viewLoginName = 'capp/login';

    /**
     * View
     *
     * @var CView_View}string
     */
    private $view;

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
        if (!$this->isUserLogin() && $this->config('have_user_login') && $this->loginRequired) {
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

            $themePath = CManager::theme()->getThemePath();

            if (CView::exists($themePath . $viewName)) {
                $v = CView::factory($themePath . $viewName);
            }
            if ($v == null) {
                if (!CView::exists($viewName)) {
                    throw new CApp_Exception('view :viewName not exists', [':viewName' => $viewName]);
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
        if (!$this->isUserLogin() && $this->config('have_user_login') && $this->loginRequired) {
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
