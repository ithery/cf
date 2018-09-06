<?php

class CFactory {

    use CTrait_Compat_Factory;

    /**
     * 
     * @param string $id
     * @param string $type
     * @return CElement_FormInput
     * @throws CException
     */
    public static function createControl($id, $type) {
        $control = null;
        if (CManager::instance()->isRegisteredControl($type)) {
            $control = CManager::instance()->createControl($id, $type);
        } else {
            throw new CException('Unknown control type ' . $type);
        }

        return $control;
    }

    /**
     * 
     * @param string $fieldId
     * @return CElement_Component_Form_Field
     */
    public static function createField($id = "") {
        return CElement_Factory::createComponent('Form_Field', $id);
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_DataTable
     */
    public static function createTable($id = "") {
        return CElement_Factory::createComponent('DataTable', $id);
    }

    public static function create_row($row_id = '') {
        $row = CTableRow::factory($row_id);

        return $row;
    }

    public static function create_calendar($calendar_id = "") {
        $calendar = CCalendar::factory($calendar_id);

        return $calendar;
    }

    public static function create_tab_list($tabs_id = "") {
        $tabs = CTabList::factory($tabs_id);

        return $tabs;
    }

    public static function create_tab_static_list($tabs_id = "") {
        $tabs = CTabStaticList::factory($tabs_id);

        return $tabs;
    }

    public static function create_ajax() {
        $ajax = CAjaxObject::factory();

        return $ajax;
    }

    public static function create_elm($tag, $id = "") {
        $tag = CCustomElement::factory($tag, $id);

        return $tag;
    }

    public static function create_div($id = "") {
        $div = CDivElement::factory($id);

        return $div;
    }

    public static function create_row_fluid($id = "") {
        $rowf = CRowFluid::factory($id);

        return $rowf;
    }

    public static function create_span($id = "") {
        $span = CSpan::factory($id);

        return $span;
    }

    public static function create_img($id = "") {
        $img = CImgElement::factory($id);

        return $img;
    }

    public static function create_basic_span($id = "") {
        $span = CBasicSpan::factory($id);

        return $span;
    }

    public static function create_widget($id = "") {
        $widget = CWidget::factory($id);

        return $widget;
    }

    /**
     * 
     * @param string $id
     * @return CForm
     */
    public static function create_form($id = "") {
        $form = CForm::factory($id);

        return $form;
    }

    public static function create_nestable($id = "") {
        $nestable = CNestable::factory($id);

        return $nestable;
    }

    public static function create_hr() {
        
    }

    public static function create_br() {
        
    }

    // public static function create_element($tag, $id = "") {
    // $elm = CElement::factory($id, $tag);
    // $
    // return $elm;
    // }

    public static function create_element($type, $id = "") {
        $element = null;
        if (CManager::instance()->is_registered_element($type)) {
            $element = CManager::instance()->create_element($id, $type);
        } else {
            trigger_error('Unknown element type ' . $type);
        }
        return $element;
    }

    public static function create_action_list($id = "") {
        $actlist = CActionList::factory($id);


        return $actlist;
    }

    public static function create_action($id = "") {
        $act = CAction::factory($id);

        return $act;
    }

    public static function create_pie_chart($id = "") {
        $pie_chart = CPieChartElement::factory($id);

        return $pie_chart;
    }

}

?>