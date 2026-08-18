<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_Widget_Header extends CElement_Element {
    use CTrait_Element_Property_Icon,
        CTrait_Element_Property_Title;

    /**
     * @var CElement_List_ActionList
     */
    protected $actions;

    /**
     * @var null|CElement_FormInput_Checkbox_Switcher
     */
    protected $switcher;

    /**
     * @var null|CElement_Element_Div
     */
    protected $switcherWrapper;

    /**
     * @var CElement_Element_Div
     */
    protected $titleWrapper;

    /**
     * 'hide' or 'block', see setSwitcherBehaviour().
     *
     * @var string
     */
    protected $switcherBehaviour = 'hide';

    /**
     * Overlay message shown while blocked, switcherBehaviour 'block' only.
     *
     * @var string
     */
    protected $switcherBlockMessage = '';

    /**
     * CSS class for this header element, from the 'widget.class.header' theme setting.
     *
     * @var string
     */
    protected $headerClass;

    /**
     * Whether the collapse/expand toggle is rendered, see
     * CElement_Component_Widget::setCollapse() (the only caller of setCollapsible()).
     *
     * @var bool
     */
    protected $collapsible = false;

    /**
     * @param string $id
     * @param string $tag
     */
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
            $this->actions = new CElement_List_ActionList($this->parent->id . '_header');
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
        $action = new CElement_Component_Action($id);
        $this->actions()->add($action);

        return $action;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setCollapsible($bool = true) {
        $this->collapsible = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCollapsible() {
        return $this->collapsible;
    }

    /**
     * @return void
     */
    public function build() {
        $this->addClass($this->headerClass . ' clearfix');
        if ($this->actions != null) {
            $this->addClass('with-elements');
        }
        if (strlen($this->originalIcon) > 0) {
            $this->titleWrapper->addSpan()->addClass('icon')->addIcon()->setIcon($this->originalIcon);
        }
        $this->titleWrapper->addH5()->add($this->title);

        if ($this->collapsible) {
            $this->addA()->addClass('widget-collapse-toggle ml-auto')->setAttr('href', 'javascript:;')
                ->addIcon()->setIcon('fa fa-chevron-up');
        }
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

    /**
     * @param string $behaviour 'hide' or 'block'
     *
     * @return $this
     */
    public function setSwitcherBehaviour($behaviour = 'hide') {
        $this->switcherBehaviour = $behaviour;

        return $this;
    }

    /**
     * @param string $blockMessage
     *
     * @return $this
     */
    public function setSwitcherBlockMessage($blockMessage = '') {
        $this->switcherBlockMessage = $blockMessage;

        return $this;
    }

    /**
     * @return bool
     */
    public function haveSwitcher() {
        if ($this->switcher) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Emits the switcher's show/hide (or block/unblock) click behavior as an
     * inline script -- the collapse toggle (build(), above) has no
     * counterpart here, its behavior is handled entirely by cres.js instead.
     *
     * @param int $indent
     *
     * @return string
     */
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
