<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 28, 2020
 */

class CElement_View extends CElement {
    /**
     * @var CView_View
     */
    protected $view;

    protected $viewElement;

    public function __construct($id, $view = null, $data = []) {
        parent::__construct($id);
        if ($view != null) {
            $this->setView($view, $data);
        }
        $this->viewElement = [];
    }

    public function setView($view, $data = null) {
        if ($view != null) {
            if (!($view instanceof CView_View)) {
                if ($data == null) {
                    $data = [];
                }
                $view = CView::factory($view, $data);
            }
            if ($data !== null) {
                $view->set($data);
            }
        }

        $this->view = $view;
    }

    public function html($indent = 0) {
        CApp::setRenderingElement($this);
        if ($this->view != null) {
            return $this->view->render();
        }
    }

    public function js($indent = 0) {
        return '';
    }

    public function viewElement($key) {
        if (!isset($this->viewElement[$key])) {
            $this->viewElement[$key] = new CElement_PseudoElement();
        }
        return $this->viewElement[$key];
    }
}
