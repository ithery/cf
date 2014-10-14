<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CWidget extends CElement {

    protected $header_action_list;
    public $title;
    public $content;
    public $span;
    public $scroll;
    public $icon;
    public $wrapped;
    public $nopadding;
    public $info;
    public $info_tip;
    public $header_action_style;
    public $height;

    public function __construct($id) {
        parent::__construct($id);

        $this->icon = "th-list";
        $this->title = "";
        $this->height = "";
        $this->content = "";
        $this->span = 12;
        $this->wrapped = true;
        $this->scroll = false;
        $this->info = "";
        $this->info_tip = "";
        $this->nopadding = false;
        $this->header_action_list = CActionList::factory();
        $this->header_action_style = 'widget-action';
        $this->header_action_list->set_style('widget-action');
    }
	
	
	
    public static function factory($id = "") {
        return new CWidget($id);
    }

    public function have_header_action() {
        //return $this->can_edit||$this->can_delete||$this->can_view;
        return $this->header_action_list->child_count() > 0;
    }

    public function add_header_action($id = "") {
        $row_act = CAction::factory($id);
        $this->header_action_list->add($row_act);
        return $row_act;
    }

    public function add_header_action_list($id = "") {
        $row_acts = CActionList::factory($id);
        $row_acts->set_style('btn-dropdown');
        $this->header_action_list->add($row_acts);
        return $row_acts;
    }
	public function set_header_action_style($style) {
        $this->header_action_style = $style;
        $this->header_action_list->set_style($style);
    }

    public function set_title($title) {
        $this->title = $title;
        return $this;
    }

    public function set_height($height) {
        $this->height = $height;
        return $this;
    }

    public function set_icon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function set_scroll($bool) {
        $this->scroll = $bool;
        return $this;
    }

    public function set_nopadding($bool) {
        $this->nopadding = $bool;
        return $this;
    }

    public function set_wrapped($bool) {
        $this->wrapped = $bool;
        return $this;
    }

    public function set_info($info) {
        $this->info = $info;
        return $this;
    }

    public function set_info_tip($info_tip) {
        $this->info_tip = $info_tip;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        if ($this->header_action_style == "btn-dropdown") {
			$this->header_action_list->add_class("pull-right");
		}
		if ($this->wrapped) {
            $html->appendln('<div class="row-fluid">
				<div class="span' . $this->span . '">');
        }
        $nopadding = "";
        if ($this->nopadding) {
            $nopadding = "nopadding";
        }
        $info = "";
        if (strlen($this->info) > 0) {
            $info = '<span class="label label-info tip-left " data-original-title="' . $this->info_tip . '">' . $this->info . '</span>';
        }
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        $html->appendln('<div class="widget-box ' . $classes . '" ' . $custom_css . '>');
        $html->appendln('	<div class="widget-title">');
        $html->appendln('		<span class="icon">');
        $html->appendln('		<i class="icon-' . $this->icon . '"></i>');
        $html->appendln('		</span>');
        $html->appendln('		<h5>' . $this->title . '</h5>');
        $html->appendln('		' . $info . '');
        if ($this->have_header_action()) {
            $html->appendln($this->header_action_list->html($html->get_indent()));
        }

        $scroll_class = "";
        if ($this->scroll) {
            $scroll_class = ' slimscroll';
        }
        $str_height = '';
        if (strlen($this->height) > 0) {
            $str_height = ' height="' . $this->height . 'px"';
        }
        $html->appendln('	</div>');
        $html->appendln('	<div class="widget-content ' . $nopadding . $scroll_class . '"' . $str_height . '>');
        $html->appendln('		' . parent::html() . '');
        $html->appendln('	</div>');
        $html->appendln('</div>');
        $html->br();

        if ($this->wrapped) {
            $html->appendln('</div>
			</div>');
        }
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        if ($this->have_header_action()) {
            $js->appendln($this->header_action_list->js($js->get_indent()));
        }
        $js->append(parent::js($js->get_indent()));
        return $js->text();
    }

}

