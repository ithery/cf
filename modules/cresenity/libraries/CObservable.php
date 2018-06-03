<?php

abstract class CObservable extends CRenderable {

    use CTrait_Compat_Observable;

    protected $listeners;
    protected $manager;
    protected $wrapper;

    public function getListeners() {
        return $this->listeners;
    }

    /**
     * 
     * @param string $event
     * @return CListener
     */
    public function addListener($event) {
        $listener = CListener::factory($this->id, $event);
        $this->listeners[] = $listener;
        return $listener;
    }

    protected function __construct($id = "") {

        parent::__construct($id);
        $this->wrapper = $this;
        $this->listeners = array();
        $this->manager = CManager::instance();

        $this->manager->registerControl('text', 'CElement_FormInput_Text');
        $this->manager->registerControl('datepicker', 'CFormInputDate');
        $this->manager->registerControl('date', 'CFormInputDate');
        $this->manager->registerControl('currency', 'CFormInputCurrency');
        $this->manager->registerControl('time', 'CFormInputTimePicker');
        $this->manager->registerControl('timepicker', 'CFormInputTimePicker');
        $this->manager->registerControl('clockpicker', 'CElement_FormInput_ClockPicker');
        $this->manager->registerControl('image', 'CFormInputImage');
        $this->manager->registerControl('image-ajax', 'CElement_FormInput_ImageAjax');
        $this->manager->registerControl('multi-image-ajax', 'CElement_FormInput_MultipleImageAjax');
        $this->manager->registerControl('file', 'CFormInputFile');
        $this->manager->registerControl('password', 'CFormInputPassword');
        $this->manager->registerControl('textarea', 'CElement_FormInput_Textarea');
        $this->manager->registerControl('select', 'CFormInputSelect');
        $this->manager->registerControl('select-tag', 'CFormInputSelectTag');
        $this->manager->registerControl('selectsearch', 'CFormInputSelectSearch');
        $this->manager->registerControl('label', 'CFormInputLabel');
        $this->manager->registerControl('checkbox', 'CFormInputCheckbox');
        $this->manager->registerControl('checkbox-list', 'CFormInputCheckboxList');
        $this->manager->registerControl('switcher', 'CElement_FormInput_Checkbox_Switcher');
        $this->manager->registerControl('summernote', 'CElement_FormInput_Textarea_Summernote');
        $this->manager->registerControl('wysiwyg', 'CFormInputWysiwyg');
        $this->manager->registerControl('ckeditor', 'CFormInputCKEditor');
        $this->manager->registerControl('hidden', 'CFormInputHidden');
        $this->manager->registerControl('radio', 'CFormInputRadio');
        $this->manager->registerControl('filedrop', 'CFormInputFileDrop');
        $this->manager->registerControl('slider', 'CFormInputSlider');
        $this->manager->registerControl('tooltip', 'CFormInputTooltip');
        $this->manager->registerControl('fileupload', 'CFormInputFileUpload');
    }

    /**
     * @param string $eventName The name of the event
     * @param PhpExt_Listener|PhpExt_JavascriptStm $listener A {@link PhpExt_JavascriptStm} with the corresponding name of the javascript function previously defined of a {@link PhpExt_Listener} to create an anonymous function
     * @return PhpExt_Observable 
     */
    public function attachListener($event, $listener) {
        $this->listeners->add($listener, $event);
        return $this;
    }

    public function detachListener($event) {
        $this->listeners->remove($event);
    }

    /**
     * 
     * @param type $id
     * @param type $type
     * @return CFormInput
     */
    public function addControl($id, $type) {
        $control = null;
        if ($this->manager->isRegisteredControl($type)) {
            $control = $this->manager->create_control($id, $type);
        } else {
            trigger_error('Unknown control type ' . $type);
        }


        $this->wrapper->add($control);

        return $control;
    }

    /**
     * 
     * @param type $id
     * @return CElement_Template
     */
    public function addTemplate($id = "") {
        $template = CElement_Factory::createTemplate($id);
        $this->wrapper->add($template);
        return $template;
    }

    /**
     * Add Div &lt;div&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Div  Div Element
     */
    public function addDiv($id = "") {
        $element = CElement_Factory::createElement('div', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Anchor Element &lt;a&gt
     *
     * @param string $id optional
     * @return  CElement_Element_A  Anchor Element
     */
    public function addA($id = "") {
        $element = CElement_Factory::createElement('a', $id);
        $this->wrapper->add($element);
        return $element;
    }

    public function add_a($id = "") {
        return $this->addA($id);
    }

    /**
     * Add Heading 1 Element &lt;h1&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H1  Heading 1 Element
     */
    public function addH1($id = "") {
        $element = CElement_Factory::createElement('h1', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 2 Element &lt;h2&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H2  Heading 2 Element
     */
    public function addH2($id = "") {
        $element = CElement_Factory::createElement('h2', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 3 Element &lt;h3&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H3  Heading 3 Element
     */
    public function addH3($id = "") {
        $element = CElement_Factory::createElement('h3', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 4 Element &lt;h4&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H4  Heading 4 Element
     */
    public function addH4($id = "") {
        $element = CElement_Factory::createElement('h4', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 5 Element &lt;h5&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H5  Heading 5 Element
     */
    public function addH5($id = "") {
        $element = CElement_Factory::createElement('h5', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Heading 6 Element &lt;h6&gt
     *
     * @param string $id optional
     * @return  CElement_Element_H6  Heading 6 Element
     */
    public function addH6($id = "") {
        $element = CElement_Factory::createElement('h6', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Ordered List Element &lt;ol&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Ol  Ordered List Element
     */
    public function addOl($id = "") {
        $element = CElement_Factory::createElement('ol', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Unordered List Element &lt;ul&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Ul  Unordered List Element
     */
    public function addUl($id = "") {
        $element = CElement_Factory::createElement('ul', $id);
        //$element = CUlElement::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add List Item Element &lt;li&gt
     * 
     * @param string $id
     * @return CElement_Element_Ol List Item Element
     */
    public function addLi($id = "") {
        $element = CElement_Factory::createElement('li', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Iframe Element &lt;iframe&gt
     * 
     * @param string $id
     * @return CElement_Element_Iframe Iframe Element
     */
    public function addIframe($id = "") {
        $element = CElement_Factory::createElement('iframe', $id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * Add Form Field
     *
     * @param   string id
     * @return  CElement_Component_Form_Field  Form Field
     */
    public function addField($id = "") {
        $field = CElement_Factory::createComponent('Form_Field', $id);
        $this->add($field);
        return $field;
    }

    public function addFieldset($fieldset_id = "") {
        $fieldset = CFormFieldset::factory($fieldset_id);
        $this->add($fieldset);
        return $fieldset;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_DataTable
     */
    public function addTable($id = "") {
        $table = CElement_Factory::createComponent('DataTable', $id);
        $this->add($table);
        return $table;
    }

    public function addRow($row_id = '') {
        $row = CTableRow::factory($row_id);
        $this->add($row);
        return $row;
    }

    public function addCalendar($calendar_id = "") {
        $calendar = CCalendar::factory($calendar_id);
        $this->add($calendar);
        return $calendar;
    }

    public function addTabList($tabs_id = "") {
        $tabs = CTabList::factory($tabs_id);
        $this->add($tabs);
        return $tabs;
    }

    public function addTabStaticList($tabs_id = "") {
        $tabs = CTabStaticList::factory($tabs_id);
        $this->add($tabs);
        return $tabs;
    }

    public function addAjax() {
        $ajax = CAjaxObject::factory();
        $this->add($ajax);
        return $ajax;
    }

    public function addElm($tag, $id = "") {
        $tag = CCustomElement::factory($tag, $id);
        $this->add($tag);
        return $tag;
    }

    public function addRowFluid($id = "") {
        $rowf = CRowFluid::factory($id);
        $this->add($rowf);
        return $rowf;
    }

    public function addSpan($id = "") {
        $span = CSpan::factory($id);
        $this->add($span);
        return $span;
    }

    public function addImg($id = "") {
        $img = CImgElement::factory($id);
        $this->add($img);
        return $img;
    }

    public function addBasicSpan($id = "") {
        $span = CBasicSpan::factory($id);
        $this->add($span);
        return $span;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Widget
     */
    public function addWidget($id = "") {
        $widget = CElement_Factory::createComponent('Widget', $id);
        $this->add($widget);
        return $widget;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Form
     */
    public function addForm($id = "") {
        $form = CElement_Factory::createComponent('Form', $id);
        $this->add($form);
        return $form;
    }

    public function addNestable($id = "") {
        $nestable = CNestable::factory($id);
        $this->add($nestable);
        return $nestable;
    }

    public function addHr() {
        $this->add('<hr />');
    }

    public function addBr() {
        $this->add('<br />');
    }

    // public function add_element($tag, $id = "") {
    // $elm = CElement::factory($id, $tag);
    // $this->add($elm);
    // return $elm;
    // }

    public function addElement($type, $id = "") {
        $element = null;
        if ($this->manager->is_registered_element($type)) {
            $element = $this->manager->create_element($id, $type);
        } else {
            trigger_error('Unknown element type ' . $type);
        }



        $this->add($element);

        return $element;
    }

    /**
     * 
     * 
     * @param string $id
     * @return CElement_List_ActionList
     */
    public function addActionList($id = "") {
        $actlist = CElement_Factory::createList('ActionList', $id);
        $this->add($actlist);
        if ($this instanceof CElement_Component_Form) {
            $actlist->setStyle('form-action');
        }
        return $actlist;
    }

    public function addAction($id = "") {
        $act = CAction::factory($id);
        $this->add($act);
        return $act;
    }

    public function addPieChart($id = "") {
        $pie_chart = CPieChartElement::factory($id);
        $this->add($pie_chart);
        return $pie_chart;
    }

    public function addDashboard($id = "") {
        $dashboard = CDashboard::factory($id);
        $this->add($dashboard);
        return $dashboard;
    }

    public function clearBoth() {
        $this->add('<div class="clear-both"></div>');
    }

    public function setHandlerUrlParam($param) {

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

    public function regenerateId($recursive = false) {
        $before_id = $this->id;

        parent::regenerateId($recursive);
        //we change the owner of listener
        foreach ($this->listeners as $listener) {
            if ($listener->owner() == $before_id) {
                $listener->set_owner($this->id);
            }
        }
    }

}
