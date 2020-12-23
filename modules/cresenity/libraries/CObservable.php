<?php

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 5:09:28 PM
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
     * @var CObservable_Javascript
     */
    protected $javascript;

    /**
     * @return CObservable_Javascript
     */
    public function javascript() {
        return $this->javascript;
    }

    /**
     * @return CObservable_Javascript_JQuery
     */
    public function jquery() {
        return $this->javascript->jquery();
    }

    /**
     * @return CObservable_Javascript_Handler
     */
    public function handler() {
        return $this->javascript->handler();
    }

    protected function __construct($id = '') {
        parent::__construct($id);
        $this->listeners = [];
        $this->javascript = new CObservable_Javascript($this);
    }

    /**
     * @param string|CElement_FormInput $id
     * @param string                    $type
     *
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
     * @param type $id
     *
     * @return CElement_Template
     */
    public function addTemplate($id = '') {
        $template = CElement_Factory::createTemplate($id);
        $this->wrapper->add($template);
        return $template;
    }

    /**
     * @param type  $id
     * @param mixed $componentName
     *
     * @return CElement_Template
     */
    public function addComponent($componentName, $id = '') {
        $viewComponent = CElement_Factory::createViewComponent($componentName, $id);
        $this->wrapper->add($viewComponent);
        return $viewComponent;
    }

    /**
     * @param type       $view
     * @param string     $id
     * @param null|mixed $data
     *
     * @return type
     */
    public function addView($view = null, $data = null, $id = null) {
        if (strlen($id) == 0) {
            $id = 'view-' . cstr::slug($view) . '-' . CObserver::instance()->newId();
        }

        $viewElement = CElement_Factory::createView($id, $view, $data);

        $this->wrapper->add($viewElement);
        return $viewElement;
    }

    /**
     * Add Form Field
     *
     * @param string $id
     *
     * @return CElement_Component_Form_Field Form Field
     */
    public function addField($id = '') {
        $field = CElement_Factory::createComponent('Form_Field', $id);
        $this->add($field);
        return $field;
    }

    public function addRow($row_id = '') {
        $row = CTableRow::factory($row_id);
        $this->add($row);
        return $row;
    }

    public function addCalendar($calendar_id = '') {
        $calendar = CCalendar::factory($calendar_id);
        $this->add($calendar);
        return $calendar;
    }

    public function addTabStaticList($tabs_id = '') {
        $tabs = CTabStaticList::factory($tabs_id);
        $this->add($tabs);
        return $tabs;
    }

    public function addRowFluid($id = '') {
        $rowf = CRowFluid::factory($id);
        $this->add($rowf);
        return $rowf;
    }

    public function addSpan($id = '') {
        $span = CElement_Factory::createElement('span', $id);
        $this->add($span);
        return $span;
    }

    public function addBasicSpan($id = '') {
        $span = CBasicSpan::factory($id);
        $this->add($span);
        return $span;
    }

    public function addPrismCode($id = '') {
        $code = CElement_Factory::createComponent('PrismCode', $id);
        $this->add($code);
        return $code;
    }

    public function addBlockly($id = '') {
        $code = CElement_Factory::createComponent('Blockly', $id);
        $this->add($code);
        return $code;
    }

    public function addPdfViewer($id = '') {
        $code = CElement_Factory::createComponent('PdfViewer', $id);
        $this->add($code);
        return $code;
    }

    /**
     * @return $this
     */
    public function addHr() {
        $this->add('<hr />');
        return $this;
    }

    /**
     * @return $this
     */
    public function addBr() {
        $this->add('<br />');
        return $this;
    }

    public function addElement($type, $id = '') {
        $element = null;
        if (CManager::instance()->isRegisteredElement($type)) {
            $element = CManager::instance()->createElement($id, $type);
        } else {
            throw new CException('Unknow element type :elementType', [':elementType' => $type]);
        }

        $this->add($element);

        return $element;
    }

    /**
     * Add Action Element
     *
     * @param string $id optional
     *
     * @return CElement_Component_Action
     */
    public function addAction($id = '') {
        $act = CElement_Factory::createComponent('Action', $id);
        $this->add($act);
        return $act;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Alert
     */
    public function addAlert($id = '') {
        $element = CElement_Factory::createComponent('Alert', $id);
        $this->add($element);
        return $element;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Accordion
     */
    public function addAccordion($id = '') {
        $element = CElement_Factory::createComponent('Accordion', $id);
        $this->add($element);
        return $element;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Icon
     */
    public function addIcon($id = '') {
        $icon = CElement_Factory::createComponent('Icon', $id);
        $this->add($icon);
        return $icon;
    }

    /**
     * @param type $id
     *
     * @return type
     *
     * @deprecated
     */
    public function addPieChart($id = '') {
        $pie_chart = CPieChartElement::factory($id);
        $this->add($pie_chart);
        return $pie_chart;
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
