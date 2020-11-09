<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:56:50 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Widget extends CElement_Component {

    use CTrait_Compat_Element_Widget;

    /**
     *
     * @var CElement_Component_Widget_Header
     */
    protected $header;

    /**
     *
     * @var CElement_Element_Div
     */
    protected $content;
    public $scroll;
    public $nopadding;
    public $height;
    protected $switcher;
    private $collapse;
    private $close;
    private $js_collapse;

    public function __construct($id) {
        parent::__construct($id);
        $this->header = CElement_Factory::createComponent('Widget_Header');
        $this->add($this->header);
        $this->content = $this->addDiv()->addClass('widget-content clearfix');
        $this->addClass('widget-box');
        $this->wrapper = $this->content;

        $this->height = "";
        $this->scroll = false;
        $this->nopadding = false;

        $this->collapse = false;
        $this->close = false;
        $this->js_collapse = true;
    }

    public static function factory($id = "") {
        return new CElement_Component_Widget($id);
    }

    /**
     * 
     * @return CElement_Component_Widget_Header
     */
    public function header() {
        return $this->header;
    }

    /**
     * 
     * @return CElement_Element_Div
     */
    public function content() {
        return $this->content;
    }

    public function addHeaderAction($id = "") {
        return $this->header()->addAction($id);
    }

    public function setHeaderActionStyle($style) {
        $this->header()->actions()->setStyle($style);
        return $this;
    }

    public function addSwitcher($id = "") {
        return $this->header->addSwitcher($id);
    }
    
    public function setSwitcherBehaviour($behaviour='hide') {
        $this->header->setSwitcherBehaviour($behaviour);
        return $this;
    }

     public function setSwitcherBlockMessage($blockMessage='') {
        $this->header->setSwitcherBlockMessage($blockMessage);
        return $this;
    }
    public function get_collapse() {
        return $this->collapse;
    }

    public function get_close() {
        return $this->close;
    }

    public function set_collapse($collapse, $js_collapse = false) {
        $this->collapse = $collapse;
        $this->js_collapse = $js_collapse;
        return $this;
    }

    function set_close($close) {
        $this->close = $close;
        return $this;
    }

    /**
     * Set the title of the widget
     * 
     * @param string $title
     * @param string $lang
     * @return $this
     */
    public function setTitle($title, $lang = true) {
        $this->header()->setTitle($title, $lang);
        return $this;
    }
    
    /**
     * Set the icon of the widget
     * 
     * @param string $title
     * @param string $lang
     * @return $this
     */
    public function setIcon($icon) {
        $this->header()->setIcon($icon);
        return $this;
    }

    public function set_height($height) {
        $this->height = $height;
        return $this;
    }

    public function set_scroll($bool) {
        $this->scroll = $bool;
        return $this;
    }

    public function setNoPadding($bool = true) {
        $this->nopadding = $bool;
        return $this;
    }

    public function build() {
        if ($this->nopadding) {
            $this->content->addClass('nopadding p-0');
        }
        
    }

   
}
