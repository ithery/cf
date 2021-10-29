<?php

class CView_Exception_ViewException extends ErrorException {
    protected $viewData = [];

    protected $view = '';

    public function setViewData(array $data) {
        $this->viewData = $data;
    }

    public function getViewData() {
        return $this->viewData;
    }

    public function setView($path) {
        $this->view = $path;
    }

    protected function dumpViewData($variable) {
        return (new CView_HtmlDumper())->dumpVariable($variable);
    }

    public function context() {
        $context = [
            'view' => [
                'view' => $this->view,
            ],
        ];

        $context['view']['data'] = array_map([$this, 'dumpViewData'], $this->viewData);

        return $context;
    }
}
