<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2019, 2:08:19 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_DateTime_MaterialDateTime extends CElement_FormInput_DateTime {

    public function __construct($id) {
        parent::__construct($id);
        CManager::instance()->registerModule('bootstrap-4-material-datepicker');
    }

    public function build() {
        parent::build();
        $this->addClass('form-control');
    }

    public function js($indent = 0) {

        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append(parent::js($indent))->br();

        $option = " weekStart: 1";
        //$option .= " ,format : 'dddd DD MMMM YYYY - HH:mm'";
        //$option .= " ,shortTime: true";
        //$option .= " ,nowButton : true";
        //$option .= " ,minDate : new Date()";


        $js->append("$('#" . $this->id . "').bootstrapMaterialDatePicker({" . $option . "});")->br();
        return $js->text();
    }

}
