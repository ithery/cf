<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 20, 2018, 1:45:20 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Widget_Header extends CElement_Element {

    use CTrait_Element_Property_Icon,
        CTrait_Element_Property_Title;

    /**
     *
     * @var CElement_List_ActionList
     */
    protected $actions;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->icon = "";
        $this->title = "";
    }

    public function actions() {
        if ($this->actions == null) {
            $this->actions = CElement_Factory::createList('ActionList', $this->parent->id . '_header');
            $this->actions->setStyle('widget-action');
            $this->add($this->actions);
        }
        return $this->actions;
    }

    public function addAction($id = "") {
        $action = CElement_Factory::createComponent('Action', $id);
        $this->actions()->add($action);
        return $action;
    }

    public function build() {
        $this->addClass('widget-title');
        if (strlen($this->icon) > 0) {
            $this->addSpan()->addClass('icon')->addIcon($this->icon);
        }
        $this->addH5()->add($this->title);
    }

}
