<?php

class CApp {
    /**
     * @param null|string $id
     *
     * @return CElement_Component_Widget
     */
    public function addWidget($id = null) {
        return $this->element->addWidget($id);
    }

    /**
     * @param null|string $id
     *
     * @return CElement_Component_DataTable
     */
    public function addTable($id = null) {
        return $this->element->addTable($id);
    }

    /**
     * @param CElement_View}string $view
     * @param string               $id
     * @param null|mixed           $data
     *
     * @return CElement_View
     */
    public function addView($view = null, $data = null, $id = null) {
        return $this->element->addView($view, $data, $id);
    }
}
