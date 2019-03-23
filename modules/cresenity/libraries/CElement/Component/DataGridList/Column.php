<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 2:30:28 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_DataGridList_Column extends CObject {

    use CTrait_Element_Property_Label,
        CTrait_Element_Property_Align,
        CTrait_Element_Transform;

    public function __construct($id = "") {
        parent::__construct($id);
        $this->label = '';
        $this->align = CConstant::ALIGN_LEFT;
        $this->transforms = array();
    }

}
