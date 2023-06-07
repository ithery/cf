<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 20, 2018, 1:45:20 AM
 */
class CElement_Component_Widget_Header extends CElement_Element {
    use CTrait_Element_Property_Icon,
        CTrait_Element_Property_Title;

    /**
     * @var CElement_List_ActionList
     */
    protected $actions;

    protected $switcher;

    protected $switcherWrapper;

    protected $titleWrapper;

    protected $switcherBehaviour = 'hide';

    protected $switcherBlockMessage = '';

    protected $headerClass;

    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);
        $this->headerClass = c::theme('widget.class.header', 'widget-title');
        $this->icon = '';
        $this->title = '';
        $this->titleWrapper = $this->addDiv()->addClass('widget-title-wrapper');
    }

    /**
     * @return CElement_Component_Widget
     */
    public function getWidget() {
        return $this->parent;
    }

    /**
     * @return CElement_List_ActionList
     */
    public function actions() {
        if ($this->actions == null) {
            $this->actions = CElement_Factory::createList('ActionList', $this->parent->id . '_header');
            $this->actions->setStyle('widget-action')->addClass('ml-auto');
            $this->add($this->actions);
        }

        return $this->actions;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Action
     */
    public function addAction($id = '') {
        $action = CElement_Factory::createComponent('Action', $id);
        $this->actions()->add($action);

        return $action;
    }

    public function build() {
        $this->addClass($this->headerClass . ' clearfix');
        if ($this->actions != null) {
            $this->addClass('with-elements');
        }
        if (strlen($this->originalIcon) > 0) {
            $this->titleWrapper->addSpan()->addClass('icon')->addIcon()->setIcon($this->originalIcon);
        }
        $this->titleWrapper->addH5()->add($this->title);
    }

    /**
     * @param null|string $id
     *
     * @return CElement_FormInput_Checkbox_Switcher
     */
    public function addSwitcher($id = null) {
        if ($this->switcher == null) {
            $this->switcherWrapper = $this->addDiv()->addClass('widget-switcher-wrapper pull-right');
            $this->switcher = CElement_Factory::createControl($id, 'switcher');
            $this->switcherWrapper->add($this->switcher);
        }

        return $this->switcher;
    }

    public function setSwitcherBehaviour($behaviour = 'hide') {
        $this->switcherBehaviour = $behaviour;

        return $this;
    }

    public function haveSwitcher() {
        if ($this->switcher) {
            return true;
        } else {
            return false;
        }
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);

        if ($this->haveSwitcher()) {
            if ($this->switcherBehaviour == 'block') {
                $js->appendln('
                    var blockMessage = "' . $this->switcherBlockMessage . '";
                    if (jQuery("#' . $this->switcher->id . '").prop("checked")) {
                        cresenity.unblockElement(jQuery("#' . $this->parent->id . '").find(".widget-content"));
                    } else {
                        cresenity.blockElement(jQuery("#' . $this->parent->id . '").find(".widget-content"),{innerMessage:blockMessage});
                    }

                    jQuery("#' . $this->switcher->id . '").click(function() {
                        if (jQuery("#' . $this->switcher->id . '").prop("checked")) {
                            cresenity.unblockElement(jQuery("#' . $this->parent->id . '").find(".widget-content"));
                        } else {
                            cresenity.blockElement(jQuery("#' . $this->parent->id . '").find(".widget-content"),{innerMessage:blockMessage});
                        }
                    });
                ');
            } else {
                $js->appendln('
                    if (jQuery("#' . $this->switcher->id . '").prop("checked")) {
                        jQuery("#' . $this->parent->id . '").find(".widget-content").show();
                    } else {
                        jQuery("#' . $this->parent->id . '").find(".widget-content").hide();
                    }

                    jQuery("#' . $this->switcher->id . '").click(function() {
                        if (jQuery("#' . $this->switcher->id . '").prop("checked")) {
                            jQuery("#' . $this->parent->id . '").find(".widget-content").show();
                        } else {
                            jQuery("#' . $this->parent->id . '").find(".widget-content").hide();
                        }
                    });
                ');
            }
        }
        $js->append($this->jsChild($js->getIndent()));

        return $js->text();
    }
}
