<?php

trait CElement_Trait_UseViewTrait {
    /**
     * @var CView_View
     */
    protected $view;

    protected $viewElement;

    protected $htmlJs;

    protected $viewData;

    protected function resolveView() {
        $view = $this->view;
        if ($view != null) {
            $viewData = $this->viewData;
            if (!($view instanceof CView_View)) {
                if ($viewData == null) {
                    $viewData = [];
                }
                $view = CView::factory($view, $viewData);
            } else {
                if ($viewData !== null) {
                    $view->set($viewData);
                }
            }
        }

        return $view;
    }

    protected function setView($view, $viewData = null) {
        $this->view = $view;
        $this->viewData = $viewData;
    }

    /**
     * Set Data to View.
     *
     * @param array $viewData
     *
     * @return $this
     */
    protected function setData(array $viewData) {
        $this->viewData = $viewData;

        return $this;
    }

    public function getVar($key) {
        return carr::get($this->viewData, $key);
    }

    public function setVar($key, $val) {
        $this->viewData[$key] = $val;

        return $this;
    }

    private function collectHtmlJsOnce() {
        if ($this->htmlJs == null) {
            if ($this->onBeforeParse != null) {
                $callable = $this->onBeforeParse;
                $callable();
            }
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

    public function onBeforeParse(callable $callable) {
        $this->onBeforeParse = $callable;

        return $this;
    }

    public function getViewHtml($indent = 0) {
        return carr::get($this->collectHtmlJsOnce(), 'html');
    }

    public function getViewJs($indent = 0) {
        return carr::get($this->collectHtmlJsOnce(), 'js');
    }
}
