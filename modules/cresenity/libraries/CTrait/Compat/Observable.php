<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:40:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Observable {

    public function get_listeners() {
        return $this->getListeners();
    }

    public function add_listener($event) {
        return $this->addListener($event);
    }

    public function attach_listener($event, $listener) {
        return $this->attachListeners($event, $listener);
    }

    public function detach_listener($event) {
        return $this->detachListener($event);
    }

    public function add_control($id, $type) {
        return $this->addControl($id, $type);
    }

    public function add_div($id = "") {
        return $this->addDiv($id);
    }

    public function add_a($id = "") {
        return $this->addA($id);
    }

    public function add_h1($id = "") {
        return $this->addH1($id);
    }

    public function add_h2($id = "") {
        return $this->addH2($id);
    }

    public function add_h3($id = "") {
        return $this->addH3($id);
    }

    public function add_h4($id = "") {
        return $this->addH4($id);
    }

    public function add_h5($id = "") {
        return $this->addH5($id);
    }

    public function add_h6($id = "") {
        return $this->addH6($id);
    }

    public function add_ol($id = "") {
        return $this->addOl($id);
    }

    public function add_ul($id = "") {
        return $this->addUl($id);
    }

    public function add_li($id = "") {
        return $this->addLi($id);
    }

    public function add_iframe($id = "") {
        return $this->addIframe($id);
    }

    public function add_field($field_id = "") {
        return $this->addField($field_id);
    }

    public function add_fieldset($fieldset_id = "") {
        return $this->addFieldset($fieldset_id);
    }

    public function add_table($table_id = "") {
        return $this->addTable($table_id);
    }

    public function add_row($row_id = '') {
        return $this->addRow($row_id);
    }

    public function add_calendar($calendar_id = "") {
        return $this->addCalendar($calendar_id);
    }

    /**
     * 
     * @param string $tabs_id
     * @return CTabList
     */
    public function add_tab_list($tabs_id = "") {
        return $this->addTabList($tabs_id);
    }

    public function add_tab_static_list($tabs_id = "") {
        return $this->addTabStaticList($tabs_id);
    }

    public function add_elm($tag, $id = "") {
        return $this->addElm($tag, $id);
    }

    public function add_row_fluid($id = "") {
        return $this->addRowFluid($id);
    }

    public function add_span($id = "") {
        return $this->addSpan($id);
    }

    public function add_img($id = "") {
        return $this->addImg($id);
    }

    public function add_basic_span($id = "") {
        return $this->addBasicSpan($id);
    }

    /**
     * @deprecated Please use addWidget
     * @param string $id
     * @return CElement_Component_Widget
     */
    public function add_widget($id = "") {
        return $this->addWidget($id);
    }

    /**
     * 
     * @deprecated Please use addForm
     * @param string $id
     * @return CElement_Component_Form;
     */
    public function add_form($id = "") {
        return $this->addForm($id);
    }

    public function add_nestable($id = "") {
        return $this->addNestable($id);
    }

    public function add_hr() {
        return $this->addHr();
    }

    public function add_br() {
        return $this->addBr();
    }

    public function add_element($type, $id = "") {
        return $this->addElement($type, $id);
    }

    public function add_action_list($id = "") {
        return $this->addActionList($id);
    }

    public function add_action($id = "") {
        return $this->addAction($id);
    }

    public function add_pie_chart($id = "") {
        return $this->addPieChart($id);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function addDashboard
     * @return CElement
     */
    public function add_dashboard($id = "") {
        return $this->addDashboard($id);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function clearBoth
     * @return $this
     */
    public function clear_both() {
        return $this->clearBoth();
    }

    /**
     * 
     * @deprecated since version 1.2, please use function setHandlerUrlParam
     * @return $this
     */
    public function set_handler_url_param($param) {
        return $this->setHandlerUrlParam($param);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function regenerateId
     * @return $this
     */
    public function regenerate_id($recursive = false) {
        return $this->regenerateId($recursive);
    }

}
