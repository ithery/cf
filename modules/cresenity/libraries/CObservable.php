<?php

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 5:09:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CObservable extends CRenderable {

    use CTrait_Compat_Observable,
        CObservable_Trait_ComponentTrait,
        CObservable_Trait_EventsTrait;

    /**
     *
     * @var CRenderable_Listener[]
     */
    protected $listeners;
    protected $manager;

    /**
     *
     * @var CObservable_Javascript
     */
    protected $javascript;

    public function getListeners() {
        return $this->listeners;
    }

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
     * @param string $event
     * @return CObservable_Listener
     */
    public function addListener($event) {
        $listener = new CObservable_Listener($this->id, $event);
        $this->listeners[] = $listener;
        return $listener;
    }

    protected function __construct($id = "") {

        parent::__construct($id);
        $this->listeners = array();
        $this->manager = CManager::instance();

        $this->manager->registerControl('text', 'CElement_FormInput_Text');
        $this->manager->registerControl('number', 'CElement_FormInput_Number');
        $this->manager->registerControl('email', 'CElement_FormInput_Email');
        $this->manager->registerControl('datepicker', 'CElement_FormInput_Date');
        $this->manager->registerControl('date', 'CElement_FormInput_Date');
        $this->manager->registerControl('material-datetime', 'CElement_FormInput_DateTime_MaterialDateTime');
        $this->manager->registerControl('daterange-picker', 'CElement_FormInput_DateRange');
        $this->manager->registerControl('currency', 'CElement_FormInput_Currency');
        $this->manager->registerControl('auto-numeric', 'CElement_FormInput_AutoNumeric');
        $this->manager->registerControl('time', 'CElement_FormInput_Time');
        $this->manager->registerControl('timepicker', 'CElement_FormInput_Time');
        $this->manager->registerControl('clock', 'CElement_FormInput_Clock');
        $this->manager->registerControl('clockpicker', 'CElement_FormInput_Clock');
        $this->manager->registerControl('image', 'CElement_FormInput_Image');
        $this->manager->registerControl('image-ajax', 'CElement_FormInput_ImageAjax');
        $this->manager->registerControl('multi-image-ajax', 'CElement_FormInput_MultipleImageAjax');
        $this->manager->registerControl('file', 'CFormInputFile');
        $this->manager->registerControl('password', 'CElement_FormInput_Password');
        $this->manager->registerControl('textarea', 'CElement_FormInput_Textarea');
        $this->manager->registerControl('select', 'CElement_FormInput_Select');

        $this->manager->registerControl('select-tag', 'CElement_FormInput_SelectTag');
        //$this->manager->registerControl('select-tag', 'CFormInputSelectTag');

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
     * @param type $id
     * @param type $type
     * @return CElement_FormInput
     */
    public function addControl($id, $type) {
        $control = null;
        if ($this->manager->isRegisteredControl($type)) {
            $control = $this->manager->createControl($id, $type);
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
     * Add Label &lt;label&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Label  Label Element
     */
    public function addLabel($id = "") {
        $element = CElement_Factory::createElement('label', $id);
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
     * Add Paragraph Element &lt;p&gt
     *
     * @param string $id optional
     * @return  CElement_Element_P  Paragraph Element
     */
    public function addP($id = "") {
        $element = CElement_Factory::createElement('p', $id);
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
     * Add Code Element &lt;ul&gt
     *
     * @param string $id optional
     * @return  CElement_Element_Code  Code Element
     */
    public function addCode($id = "") {
        $element = CElement_Factory::createElement('code', $id);
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

    /**
     * 
     * @param string $tabs_id
     * @return CTabList
     */
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
        $span = CElement_Factory::createElement('span', $id);
        $this->add($span);
        return $span;
    }

    public function add_span($id = "") {
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

    public function addPrismCode($id = "") {
        $code = CElement_Factory::createComponent('PrismCode', $id);
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
        if ($this->manager->isRegisteredElement($type)) {
            $element = $this->manager->createElement($id, $type);
        } else {
            throw new CException('Unknow element type :element_type', array(':element_type' => $type));
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
                $listener->setOwner($this->id);
            }
        }
    }

}
