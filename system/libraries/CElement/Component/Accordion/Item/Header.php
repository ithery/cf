<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 5:27:51 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Accordion_Item_Header extends CElement_Component {

    use CTrait_Element_Property_Icon,
        CTrait_Element_Property_Title;

    /**
     *
     * @var CElement_List_ActionList
     */
    protected $actions;
    protected $targetBody = '';

    public function __construct($id) {
        parent::__construct($id);

        $this->addClass('component-accordion-item-header card-header');
    }

    public function actions() {
        if ($this->actions == null) {
            $this->actions = CElement_Factory::createList('ActionList', $this->parent->id . '_header');
            $this->actions->setStyle('widget-action')->addClass('float-right pull-right');
            $this->add($this->actions);
        }
        return $this->actions;
    }

    public function addAction($id = "") {
        $action = CElement_Factory::createComponent('Action', $id);
        $this->actions()->add($action);
        return $action;
    }

    public function setTargetBody($targetBody) {
        $this->targetBody = $targetBody;
    }

    public function build() {


        if (strlen($this->icon) > 0) {
            $this->addSpan()->addClass('icon')->addIcon($this->icon);
        }
        $a = $this->addA()->add($this->getTranslationTitle());
        $a->setAttr('data-toggle', 'collapse')->setAttr('href', '#' . $this->targetBody);
    }

}
