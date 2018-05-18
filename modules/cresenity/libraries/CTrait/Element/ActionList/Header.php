<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:55:30 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_ActionList_Header {

    protected $headerActionList;

    public function headerActionCount() {
        if ($this->headerActionList != null) {
            return $this->headerActionList->childCount();
        }
        return 0;
    }

    public function haveHeaderAction() {
        return $this->headerActionCount() > 0;
    }

    public function addHeaderAction($id = "") {
        $row_act = CElement_Factory::createComponent('Action', $id);
        $this->headerActionList->add($row_act);
        return $row_act;
    }

    public function setHeaderActionStyle($style) {
        $this->headerActionList->setStyle($style);
        return $this;
    }

    public function getHeaderActionList() {
        return $this->headerActionList;
    }

}
