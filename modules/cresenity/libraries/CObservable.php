<?php

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 5:09:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CObservable extends CRenderable {

    use CTrait_Compat_Observable,
        CObservable_Trait_ElementTrait,
        CObservable_Trait_ComponentTrait,
        CObservable_Trait_ListTrait,
        CObservable_Trait_EventsTrait,
        CObservable_Trait_ListenerTrait;

    protected $manager;

    /**
     *
     * @var CObservable_Javascript
     */
    protected $javascript;

    /**
     * 
     * @return CObservable_Javascript
     */
    public function javascript() {
        return $this->javascript;
    }

    /**
     * 
     * @return CObservable_Javascript_JQuery
     */
    public function jquery() {
        return $this->javascript->jquery();
    }

    /**
     * 
     * @return CObservable_Javascript_Handler
     */
    public function handler() {
        return $this->javascript->handler();
    }

    protected function __construct($id = "") {

        parent::__construct($id);
        $this->listeners = array();
        $manager = CManager::instance();

        $manager->registerControl('text', 'CElement_FormInput_Text');
        $manager->registerControl('number', 'CElement_FormInput_Number');
        $manager->registerControl('email', 'CElement_FormInput_Email');
        $manager->registerControl('datepicker', 'CElement_FormInput_Date');
        $manager->registerControl('date', 'CElement_FormInput_Date');
        $manager->registerControl('material-datetime', 'CElement_FormInput_DateTime_MaterialDateTime');
        $manager->registerControl('daterange-picker', 'CElement_FormInput_DateRange');
        $manager->registerControl('daterange-dropdown', 'CElement_FormInput_DateRange_Dropdown');
        $manager->registerControl('daterange-button', 'CElement_FormInput_DateRange_DropdownButton');
        $manager->registerControl('currency', 'CElement_FormInput_Currency');
        $manager->registerControl('auto-numeric', 'CElement_FormInput_AutoNumeric');
        $manager->registerControl('time', 'CElement_FormInput_Time');
        $manager->registerControl('timepicker', 'CElement_FormInput_Time');
        $manager->registerControl('clock', 'CElement_FormInput_Clock');
        $manager->registerControl('clockpicker', 'CElement_FormInput_Clock');
        $manager->registerControl('image', 'CElement_FormInput_Image');
        $manager->registerControl('image-ajax', 'CElement_FormInput_ImageAjax');
        $manager->registerControl('multi-image-ajax', 'CElement_FormInput_MultipleImageAjax');
        $manager->registerControl('file', 'CFormInputFile');
        $manager->registerControl('file-ajax', 'CElement_FormInput_FileAjax');
        $manager->registerControl('password', 'CElement_FormInput_Password');
        $manager->registerControl('textarea', 'CElement_FormInput_Textarea');
        $manager->registerControl('select', 'CElement_FormInput_Select');
        $manager->registerControl('minicolor', 'CElement_FormInput_MiniColor');
        $manager->registerControl('map-picker', CElement_FormInput_MapPicker::class);

        $manager->registerControl('select-tag', 'CElement_FormInput_SelectTag');

        $manager->registerControl('selectsearch', 'CFormInputSelectSearch');
        $manager->registerControl('label', 'CFormInputLabel');
        $manager->registerControl('checkbox', CElement_FormInput_Checkbox::class);
        $manager->registerControl('checkbox-list', 'CFormInputCheckboxList');
        $manager->registerControl('switcher', 'CElement_FormInput_Checkbox_Switcher');
        $manager->registerControl('summernote', 'CElement_FormInput_Textarea_Summernote');
        $manager->registerControl('quill', 'CElement_FormInput_Textarea_Quill');
        $manager->registerControl('wysiwyg', 'CFormInputWysiwyg');
        $manager->registerControl('ckeditor', 'CFormInputCKEditor');
        $manager->registerControl('hidden', 'CFormInputHidden');
        $manager->registerControl('radio', 'CFormInputRadio');
        $manager->registerControl('filedrop', 'CFormInputFileDrop');
        $manager->registerControl('slider', 'CFormInputSlider');
        $manager->registerControl('tooltip', 'CFormInputTooltip');
        $manager->registerControl('fileupload', 'CFormInputFileUpload');

        $this->javascript = new CObservable_Javascript($this);
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
     * @param string|CElement_FormInput $id
     * @param string $type
     * @return CElement_FormInput
     */
    public function addControl($id, $type = 'text') {
        $control = null;
        if ($id instanceof CElement_FormInput) {
            $control = $id;
        }
        if ($control == null) {
            if (CManager::instance()->isRegisteredControl($type)) {
                $control = CManager::instance()->createControl($id, $type);
            } else {
                throw new CException('Unknown control type :type', [':type' => $type]);
            }
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

    public function addTabStaticList($tabs_id = "") {
        $tabs = CTabStaticList::factory($tabs_id);
        $this->add($tabs);
        return $tabs;
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
        $span = CElement_Factory::createElement('span', $id);
        $this->add($span);
        return $span;
    }

    public function add_span($id = "") {
        $span = CSpan::factory($id);
        $this->add($span);
        return $span;
    }

    public function addBasicSpan($id = "") {
        $span = CBasicSpan::factory($id);
        $this->add($span);
        return $span;
    }

    public function addPrismCode($id = "") {
        $code = CElement_Factory::createComponent('PrismCode', $id);
        $this->add($code);
        return $code;
    }

    public function addBlockly($id = "") {
        $code = CElement_Factory::createComponent('Blockly', $id);
        $this->add($code);
        return $code;
    }

    /**
     * 
     * @return $this
     */
    public function addHr() {
        $this->add('<hr />');
        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function addBr() {
        $this->add('<br />');
        return $this;
    }

    // public function add_element($tag, $id = "") {
    // $elm = CElement::factory($id, $tag);
    // $this->add($elm);
    // return $elm;
    // }

    public function addElement($type, $id = "") {
        $element = null;
        if (CManager::instance()->isRegisteredElement($type)) {
            $element = CManager::instance()->createElement($id, $type);
        } else {
            throw new CException('Unknow element type :elementType', array(':elementType' => $type));
        }



        $this->add($element);

        return $element;
    }

    /**
     * Add Action Element
     * 
     * @param string $id optional
     * @return CElement_Component_Action
     */
    public function addAction($id = "") {
        $act = CElement_Factory::createComponent('Action', $id);
        $this->add($act);
        return $act;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Alert
     */
    public function addAlert($id = "") {
        $element = CElement_Factory::createComponent('Alert', $id);
        $this->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Accordion
     */
    public function addAccordion($id = "") {
        $element = CElement_Factory::createComponent('Accordion', $id);
        $this->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CElement_Component_Icon
     */
    public function addIcon($id = "") {
        $icon = CElement_Factory::createComponent('Icon', $id);
        $this->add($icon);
        return $icon;
    }

    /**
     * 
     * @param type $id
     * @return type
     * @deprecated
     */
    public function addPieChart($id = "") {
        $pie_chart = CPieChartElement::factory($id);
        $this->add($pie_chart);
        return $pie_chart;
    }

    /**
     * 
     * @param type $id
     * @return type
     * @deprecated
     */
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

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        foreach ($this->listeners as $listener) {
            $js->appendln($listener->js($js->getIndent()));
        }


        $js->appendln(parent::js($js->getIndent()))->br();

        return $js->text();
    }

    public function regenerateId($recursive = false) {
        $beforeId = $this->id;

        parent::regenerateId($recursive);
        //we change the owner of listener
        foreach ($this->listeners as $listener) {
            if ($listener->owner() == $beforeId) {
                $listener->setOwner($this->id);
            }
        }
    }

}
