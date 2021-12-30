<?php

class CExporter_Exportable_View extends CExporter_Exportable implements CExporter_Concern_FromView {
    protected $view;

    protected $data;

    /**
     * @param CView_View|string $view
     * @param array             $data
     */
    public function __construct($view, array $data = []) {
        $this->view = $view;
        $this->data = $data;
    }

    public function resolveView() {
        if ($this->view instanceof CView_View) {
            return $this->view;
        }

        return c::view($this->view, $this->data);
    }

    public function view() {
        return $this->resolveView();
    }
}
