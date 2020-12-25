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
}
