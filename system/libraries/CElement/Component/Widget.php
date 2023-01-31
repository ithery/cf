<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:56:50 AM
 */
class CElement_Component_Widget extends CElement_Component {
    use CTrait_Compat_Element_Widget;

    public $scroll;

    public $nopadding;

    public $height;

    /**
     * @var CElement_Component_Widget_Header
     */
    protected $header;

    /**
     * @var CElement_Element_Div
     */
    protected $content;

    protected $switcher;

    private $collapse;

    private $close;

    private $js_collapse;

    private $wrapperClass;

    private $bodyClass;

    public function __construct($id) {
        parent::__construct($id);
        $this->wrapperClass = c::theme('widget.class.wrapper', 'widget-box');
        $this->bodyClass = c::theme('widget.class.body', 'widget-content');
        $this->header = CElement_Factory::createComponent(CElement_Component_Widget_Header::class);
        $this->add($this->header);
        $this->content = $this->addDiv();
        $this->wrapper = $this->content;

        $this->height = '';
        $this->scroll = false;
        $this->nopadding = false;

        $this->collapse = false;
        $this->close = false;
        $this->js_collapse = true;
    }

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

    public function setSwitcherBehaviour($behaviour = 'hide') {
        $this->header->setSwitcherBehaviour($behaviour);

        return $this;
    }

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

    public function setNoPadding($bool = true) {
        $this->nopadding = $bool;

        return $this;
    }

    public function build() {
        $this->addClass($this->wrapperClass);

        if ($this->nopadding) {
            $this->content->addClass('nopadding p-0');
        }

        $this->content->addClass($this->bodyClass . ' clearfix');
    }
}
