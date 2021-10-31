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
     * @param null|string $id
     *
     * @return CElement_Element_Div
     */
    public function addDiv($id = null) {
        return $this->element->addDiv($id);
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

    /**
     * @param null|string $id
     *
     * @return CElement_List_TabList
     */
    public function addTabList($id = null) {
        return $this->element->addTabList($id);
    }

    /**
     * @param null|string $id
     *
     * @return CElement_Component_Form
     */
    public function addForm($id = null) {
        return $this->element->addForm($id);
    }
}
