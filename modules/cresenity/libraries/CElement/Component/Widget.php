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
    protected $switcher;

    public function __construct($id) {
        parent::__construct($id);
        $this->header = CElement_Factory::createComponent('Widget_Header');
        $this->add($this->header);
        $this->content = $this->add_div()->addClass('widget-content clearfix');
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

    public function have_switcher() {
        if ($this->switcher) {
            return true;
        } else {
            return false;
        }
    }

    public function add_switcher($id = "") {
        return $this->switcher = CFactory::create_control($id, 'switcher');
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
        if ($this->have_switcher()) {
            $this->header->add('<div class="pull-right">');
            $this->header->add($this->switcher->html());
            $this->header->add('</div>');
        }
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        if ($this->have_switcher()) {
            $js->appendln('
                if (jQuery("#' . $this->switcher->get_field_id() . '").prop("checked")) {
                    jQuery("#' . $this->id . '").find(".widget-content").show();
                } else {
                    jQuery("#' . $this->id . '").find(".widget-content").hide();
                }

                jQuery("#' . $this->switcher->get_field_id() . '").click(function() {
                    if (jQuery("#' . $this->switcher->get_field_id() . '").prop("checked")) {
                        jQuery("#' . $this->id . '").find(".widget-content").show();
                    } else {
                        jQuery("#' . $this->id . '").find(".widget-content").hide();
                    }
                })
            ');
        }
        $js->append($this->jsChild($js->get_indent()));
        return $js->text();
    }

}
