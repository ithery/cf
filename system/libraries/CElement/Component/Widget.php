<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Card-style widget box: a header (title/icon/actions/switcher, see
 * CElement_Component_Widget_Header) plus a content body div.
 */
class CElement_Component_Widget extends CElement_Component {
    use CTrait_Compat_Element_Widget;

    /**
     * Whether the content body scrolls (rather than growing) when it overflows $height.
     *
     * @var bool
     */
    public $scroll;

    /**
     * Whether the content body's default padding is stripped (adds 'nopadding p-0').
     *
     * @var bool
     */
    public $nopadding;

    /**
     * CSS height applied to the content body (e.g. '300px'); empty string for auto.
     *
     * @var string
     */
    public $height;

    /**
     * @var CElement_Component_Widget_Header
     */
    protected $header;

    /**
     * @var CElement_Element_Div
     */
    protected $content;

    /**
     * Unused here -- CElement_Component_Widget_Header has its own $switcher
     * (see addSwitcher()), this property is never assigned.
     *
     * @var null|CElement_FormInput_Checkbox_Switcher
     */
    protected $switcher;

    /**
     * Whether the widget shows a collapse/expand toggle in its header --
     * false (default) renders no toggle at all. See setCollapse().
     *
     * @var bool
     */
    protected $collapse;

    /**
     * Whether the widget shows a close button. Set via the deprecated set_close()
     * (CTrait_Compat_Element_Widget); not currently read/rendered anywhere.
     *
     * @var bool
     */
    protected $close;

    /**
     * Always true in the cres.js-driven implementation -- kept only so the
     * deprecated set_collapse($collapse, $js_collapse) signature still works.
     *
     * @var bool
     */
    protected $jsCollapse;

    /**
     * CSS class(es) for the outer wrapper element, from the 'widget.class.wrapper' theme setting.
     *
     * @var string
     */
    private $wrapperClass;

    /**
     * CSS class(es) for the content body element, from the 'widget.class.body' theme setting.
     *
     * @var string
     */
    private $bodyClass;

    /**
     * Theme key for the generic base-CElement `<themeType>.classes` hook
     * (see CElement::$themeType) -- additive on top of the existing
     * 'widget.class.wrapper'/'widget.class.body' settings above, eg. a theme
     * can set `'widget' => ['classes' => 'ki-card']` for an extra class on
     * the outer wrapper without touching the wrapper/body base classes.
     *
     * @var string
     */
    protected $themeType = 'widget';

    /**
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->wrapperClass = c::theme('widget.class.wrapper', 'widget-box');
        $this->bodyClass = c::theme('widget.class.body', 'widget-content');
        $this->header = new CElement_Component_Widget_Header($this->id . '-header');
        $this->add($this->header);
        $this->content = $this->addDiv();
        $this->wrapper = $this->content;

        $this->height = '';
        $this->scroll = false;
        $this->nopadding = false;

        $this->collapse = false;
        $this->close = false;
        $this->jsCollapse = true;
    }

    /**
     * @param null|string $id
     *
     * @return static
     */
    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    /**
     * @return CElement_Component_Widget_Header
     */
    public function header() {
        return $this->header;
    }

    /**
     * @return CElement_Element_Div
     */
    public function content() {
        return $this->content;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Action
     */
    public function addHeaderAction($id = '') {
        return $this->header()->addAction($id);
    }

    /**
     * @param string $id
     *
     * @return CElement_List_ActionList
     */
    public function addHeaderActionList($id = '') {
        return $this->header()->addActionList($id);
    }

    /**
     * @param string $style
     *
     * @return $this
     */
    public function setHeaderActionStyle($style) {
        $this->header()->actions()->setStyle($style);

        return $this;
    }

    /**
     * @param null|string $id
     *
     * @return CElement_FormInput_Checkbox_Switcher
     */
    public function addSwitcher($id = '') {
        return $this->header->addSwitcher($id);
    }

    /**
     * @param string $behaviour 'hide' or 'block' (see CElement_Component_Widget_Header)
     *
     * @return $this
     */
    public function setSwitcherBehaviour($behaviour = 'hide') {
        $this->header->setSwitcherBehaviour($behaviour);

        return $this;
    }

    /**
     * @param string $blockMessage
     *
     * @return $this
     */
    public function setSwitcherBlockMessage($blockMessage = '') {
        $this->header->setSwitcherBlockMessage($blockMessage);

        return $this;
    }

    /**
     * Set the title of the widget.
     *
     * @param string     $title
     * @param bool|array $lang
     *
     * @return $this
     */
    public function setTitle($title, $lang = true) {
        $this->header()->setTitle($title, $lang);

        return $this;
    }

    /**
     * Set the icon of the widget.
     *
     * @param mixed $icon
     *
     * @return $this
     */
    public function setIcon($icon) {
        $this->header()->setIcon($icon);

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setNoPadding($bool = true) {
        $this->nopadding = $bool;

        return $this;
    }

    /**
     * Shows a collapse/expand toggle in the header (Google Drive-style
     * chevron, handled entirely client-side by cres.js, see
     * media/js/cres/src/element/component/Widget) -- off by default, no
     * toggle is rendered at all unless this is called.
     *
     * @param bool $bool
     * @param bool $jsCollapse
     *
     * @return $this
     */
    public function setCollapse($bool = true, $jsCollapse = true) {
        $this->collapse = $bool;
        $this->jsCollapse = $jsCollapse;
        $this->header()->setCollapsible($bool);

        return $this;
    }

    /**
     * @return void
     */
    public function build() {
        $this->addClass($this->wrapperClass);
        $this->addClass('cres:element:component:Widget');
        $this->setAttr('cres-element', 'component:Widget');
        $this->setAttr('cres-config', c::json([
            'collapse' => $this->collapse,
        ]));

        if ($this->nopadding) {
            $this->content->addClass('nopadding p-0');
        }

        $this->content->addClass($this->bodyClass . ' clearfix widget-collapse-content');
    }
}
