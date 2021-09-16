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

    protected $htmlJs;

    public function __construct($id, $view = null, $data = []) {
        parent::__construct($id);
        if ($view != null) {
            $this->setView($view, $data);
        }
        $this->viewElement = [];
        $this->htmlJs = null;
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

    public function collectHtmlJsOnce() {
        if ($this->htmlJs == null) {
            $html = '';
            $js = '';
            if ($this->view != null) {
                $output = $this->view->render();
                //parse the output of view
                preg_match_all('#<script>(.*?)</script>#ims', $output, $matches);

                foreach ($matches[1] as $value) {
                    $js .= $value;
                }
                $html = preg_replace('#<script>(.*?)</script>#is', '', $output);
            }

            $htmlJs = [
                'html' => $html,
                'js' => $js,
            ];
            $this->htmlJs = $htmlJs;
        }
        return $this->htmlJs;
    }

    public function html($indent = 0) {
        CApp::setRenderingElement($this);
        return carr::get($this->collectHtmlJsOnce(), 'html');
    }

    public function js($indent = 0) {
        return carr::get($this->collectHtmlJsOnce(), 'js');
    }

    public function viewElement($key) {
        if (!isset($this->viewElement[$key])) {
            $this->viewElement[$key] = new CElement_PseudoElement();
        }
        return $this->viewElement[$key];
    }
}
