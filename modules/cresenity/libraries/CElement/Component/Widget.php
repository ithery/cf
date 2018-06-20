<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 1:56:50 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Widget extends CElement_Component {

    use CTrait_Compat_Element_Widget,
        CTrait_Element_Property_Icon;

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

    public function __construct($id) {
        parent::__construct($id);
        $this->header = CElement_Factory::createComponent('Widget_Header');
        $this->add($this->header);
        $this->content = $this->add_div()->addClass('widget-content');
        $this->addClass('widget-box');
        $this->wrapper = $this->content;

        $this->height = "";
        $this->scroll = false;
        $this->nopadding = false;
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
