<?php

/**
 * @package PhpExt
 */
abstract class CObservable extends CRenderable {

    protected $listeners;
    protected $manager;

    public function get_listeners() {
        return $this->listeners;
    }

    public function add_listener($event) {
        $listener = CListener::factory($this->id, $event);
        $this->listeners[] = $listener;
        return $listener;
    }

    protected function __construct($id = "") {

        parent::__construct($id);
        $this->listeners = array();
        $this->manager = CManager::instance();

        $this->manager->register_control('text', 'CFormInputText');
        $this->manager->register_control('datepicker', 'CFormInputDate');
        $this->manager->register_control('date', 'CFormInputDate');
        $this->manager->register_control('currency', 'CFormInputCurrency');
        $this->manager->register_control('time', 'CFormInputTimePicker');
        $this->manager->register_control('timepicker', 'CFormInputTimePicker');
        $this->manager->register_control('image', 'CFormInputImage');
        $this->manager->register_control('file', 'CFormInputFile');
        $this->manager->register_control('password', 'CFormInputPassword');
        $this->manager->register_control('textarea', 'CFormInputTextarea');
        $this->manager->register_control('select', 'CFormInputSelect');
        $this->manager->register_control('select-tag', 'CFormInputSelectTag');
        $this->manager->register_control('selectsearch', 'CFormInputSelectSearch');
        $this->manager->register_control('label', 'CFormInputLabel');
        $this->manager->register_control('checkbox', 'CFormInputCheckbox');
        $this->manager->register_control('checkbox-list', 'CFormInputCheckboxList');
        $this->manager->register_control('wysiwyg', 'CFormInputWysiwyg');
        $this->manager->register_control('ckeditor', 'CFormInputCKEditor');
        $this->manager->register_control('hidden', 'CFormInputHidden');
        $this->manager->register_control('radio', 'CFormInputRadio');
        $this->manager->register_control('filedrop', 'CFormInputFileDrop');
        $this->manager->register_control('slider', 'CFormInputSlider');
        $this->manager->register_control('tooltip', 'CFormInputTooltip');
    }

    /**
     * @param string $eventName The name of the event
     * @param PhpExt_Listener|PhpExt_JavascriptStm $listener A {@link PhpExt_JavascriptStm} with the corresponding name of the javascript function previously defined of a {@link PhpExt_Listener} to create an anonymous function
     * @return PhpExt_Observable 
     */
    public function attach_listener($event, $listener) {
        $this->listeners->add($listener, $event);
        return $this;
    }

    public function detach_listener($event) {
        $this->listeners->remove($event);
    }

    /**
     * 
     * @param type $id
     * @param type $type
     * @return CFormInput
     */
    public function add_control($id, $type) {
        $control = null;
        if ($this->manager->is_registered_control($type)) {
            $control = $this->manager->create_control($id, $type);
        } else {
            trigger_error('Unknown control type ' . $type);
        }


        $this->add($control);

        return $control;
    }
    
    /**
     * Add Form Field
     *
     * @param   string  field id
     * @return  CFormField  Form Field
     */
    public function add_field($field_id = "") {
        $field = CFormField::factory($field_id);
        $this->add($field);
        return $field;
    }

    public function add_ul($id = "") {
        $ul = CUlElement::factory($id);
        $this->add($ul);
        return $ul;
    }

    public function add_li($id = "") {
        $li = CLiElement::factory($id);
        $this->add($li);
        return $li;
    }

    public function add_fieldset($fieldset_id = "") {
        $fieldset = CFormFieldset::factory($fieldset_id);
        $this->add($fieldset);
        return $fieldset;
    }

    public function add_table($table_id = "") {
        $table = CTable::factory($table_id);
        $this->add($table);
        return $table;
    }

    public function add_row($row_id = '') {
        $row = CTableRow::factory($row_id);
        $this->add($row);
        return $row;
    }

    public function add_calendar($calendar_id = "") {
        $calendar = CCalendar::factory($calendar_id);
        $this->add($calendar);
        return $calendar;
    }

    public function add_tab_list($tabs_id = "") {
        $tabs = CTabList::factory($tabs_id);
        $this->add($tabs);
        return $tabs;
    }

    public function add_tab_static_list($tabs_id = "") {
        $tabs = CTabStaticList::factory($tabs_id);
        $this->add($tabs);
        return $tabs;
    }

    public function add_ajax() {
        $ajax = CAjaxObject::factory();
        $this->add($ajax);
        return $ajax;
    }

    public function add_elm($tag, $id = "") {
        $tag = CCustomElement::factory($tag, $id);
        $this->add($tag);
        return $tag;
    }

    public function add_div($id = "") {
        $div = CDivElement::factory($id);
        $this->add($div);
        return $div;
    }

    public function add_row_fluid($id = "") {
        $rowf = CRowFluid::factory($id);
        $this->add($rowf);
        return $rowf;
    }

    public function add_span($id = "") {
        $span = CSpan::factory($id);
        $this->add($span);
        return $span;
    }

    public function add_img($id = "") {
        $img = CImgElement::factory($id);
        $this->add($img);
        return $img;
    }

    public function add_basic_span($id = "") {
        $span = CBasicSpan::factory($id);
        $this->add($span);
        return $span;
    }

    public function add_widget($id = "") {
        $widget = CWidget::factory($id);
        $this->add($widget);
        return $widget;
    }

    /**
     * 
     * @param string $id
     * @return CForm
     */
    public function add_form($id = "") {
        $form = CForm::factory($id);
        $this->add($form);
        return $form;
    }

    public function add_nestable($id = "") {
        $nestable = CNestable::factory($id);
        $this->add($nestable);
        return $nestable;
    }

    public function add_hr() {
        $this->add('<hr />');
    }

    public function add_br() {
        $this->add('<br />');
    }

    // public function add_element($tag, $id = "") {
    // $elm = CElement::factory($id, $tag);
    // $this->add($elm);
    // return $elm;
    // }

    public function add_element($type, $id = "") {
        $element = null;
        if ($this->manager->is_registered_element($type)) {
            $element = $this->manager->create_element($id, $type);
        } else {
            trigger_error('Unknown element type ' . $type);
        }



        $this->add($element);

        return $element;
    }

    public function add_action_list($id = "") {
        $actlist = CActionList::factory($id);
        $this->add($actlist);
        if ($this instanceof CForm) {
            $actlist->set_style('form-action');
        }
        return $actlist;
    }

    public function add_action($id = "") {
        $act = CAction::factory($id);
        $this->add($act);
        return $act;
    }

    public function add_pie_chart($id = "") {
        $pie_chart = CPieChartElement::factory($id);
        $this->add($pie_chart);
        return $pie_chart;
    }

    public function add_dashboard() {
        $dashboard = CDashboard::factory();
        $this->add($dashboard);
        return $dashboard;
    }

    public function clear_both() {
        $this->add('<div class="clear-both"></div>');
    }

    public function set_handler_url_param($param) {

        foreach ($this->listeners as $listener) {
            $listener->set_handler_url_param($param);
        }
    }

    public static function is_instanceof($value) {
        if (is_object($value)) {
            return ($value instanceof CObject);
        }
        return false;
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        foreach ($this->listeners as $listener) {
            $js->appendln($listener->js($js->get_indent()));
        }

        $js->appendln(parent::js($js->get_indent()))->br();

        return $js->text();
    }

    public function regenerate_id($recursive = false) {
        $before_id = $this->id;
        parent::regenerate_id($recursive);
        //we change the owner of listener
        foreach ($this->listeners as $listener) {
            if ($listener->owner() == $before_id) {
                $listener->set_owner($this->id);
            }
        }
    }

}
