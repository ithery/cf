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

    protected $data;

    public function __construct($id, $view = null, $data = []) {
        parent::__construct($id);
        if ($view != null) {
            $this->setView($view, $data);
        }
        $this->viewElement = [];
        $this->htmlJs = null;
    }

    public function resolveView() {
        $view = $this->view;
        if ($view != null) {
            if (!($view instanceof CView_View)) {
                $data = $this->data;
                if ($data == null) {
                    $data = [];
                }
                $view = CView::factory($view, $data);
            }
            if ($data !== null) {
                $view->set($data);
            }
        }

        return $view;
    }

    public function setView($view, $data = null) {
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Set Data to View.
     *
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data) {
        $this->data = $data;

        return $this;
    }

    public function collectHtmlJsOnce() {
        if ($this->htmlJs == null) {
            $view = $this->resolveView();
            $html = '';
            $js = '';
            if ($view != null) {
                $output = $view->render();
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

    /**
     * Get Element By Key.
     *
     * @param string $key
     *
     * @return CElement_PseudoElement
     */
    public function viewElement($key) {
        if (!isset($this->viewElement[$key])) {
            $this->viewElement[$key] = new CElement_PseudoElement();
        }

        return $this->viewElement[$key];
    }
}
