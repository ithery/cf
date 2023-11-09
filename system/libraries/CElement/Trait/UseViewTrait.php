<?php

trait CElement_Trait_UseViewTrait {
    /**
     * @var CView_View|string
     */
    protected $view;

    protected $htmlJs;

    protected $viewData;

    protected $onBeforeParse;

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
    public function setData(array $viewData) {
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
            $view = $this->resolveView();

            if ($this->onBeforeParse != null) {
                $callable = $this->onBeforeParse;
                $callable($view);
            }
            $html = '';
            $js = '';
            if ($view != null) {
                $output = $view->render();
                $pattern = '/<script>(.*?)<\/script>(?![^<]*<\/template>)/s';
                //parse the output of view
                preg_match_all($pattern, $output, $matches);

                foreach ($matches[1] as $value) {
                    $js .= $value;
                }
                $html = preg_replace($pattern, '', $output);
            }

            $htmlJs = [
                'html' => $html,
                'js' => $js,
            ];
            $this->htmlJs = $htmlJs;
        }

        return $this->htmlJs;
    }

    protected function onBeforeParse(callable $callable) {
        $this->onBeforeParse = $callable;

        return $this;
    }

    public function getViewHtml($indent = 0) {
        return carr::get($this->collectHtmlJsOnce(), 'html');
    }

    public function getViewJs($indent = 0) {
        return carr::get($this->collectHtmlJsOnce(), 'js');
    }

    public function html($indent = 0) {
        if (method_exists($this, 'buildOnce')) {
            $this->buildOnce();
        }

        return $this->getViewHtml();
    }

    public function js($indent = 0) {
        if (method_exists($this, 'buildOnce')) {
            $this->buildOnce();
        }

        return $this->getViewJs();
    }
}
