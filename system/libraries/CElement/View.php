<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_View extends CElement {
    use CElement_Trait_UseViewTrait;

    protected $viewElement;

    public function __construct($id, $view = null, $data = []) {
        parent::__construct($id);
        if ($view != null) {
            $this->setView($view, $data);
        }
        $this->viewElement = [];
        $this->htmlJs = null;
        $this->onBeforeParse(function (CView_View $view) {
            $view->with('__CAppElementView', $this);
        });
    }

    public function html($indent = 0) {
        return $this->getViewHtml($indent);
    }

    public function js($indent = 0) {
        return $this->getViewJs($indent);
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
