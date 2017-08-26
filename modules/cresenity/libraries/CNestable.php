<?php

class CNestable extends CElement {

    protected $data;
    protected $id_key;
    protected $value_key;
    protected $applyjs;
    protected $input;
    protected $row_action_list;
    protected $action_style;
    protected $display_callback;
    protected $requires;
    protected $js_cell;
    protected $collapse_all;
    

    public function __construct($id) {
        parent::__construct($id);

        CClientModules::instance()->register_module('jquery.nestable');
        $this->data = array();
        $this->applyjs = true;
        $this->input = '';
        $this->row_action_list = CActionList::factory();
        $this->action_style = 'btn-icon-group';
        $this->row_action_list->set_style('btn-icon-group');
        $this->display_callback = false;
        $this->requires = array();
        $this->js_cell = '';
        $this->collapse_all = false;
        
    }

    public static function factory($id) {
        return new CNestable($id);
    }

    public function display_callback_func($func, $require = "") {
        $this->display_callback = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function set_collapse_all($bool) {
        $this->collapse_all = $bool;
        return $this;
    }
    
    
    
    public function have_action() {
        //return $this->can_edit||$this->can_delete||$this->can_view;
        return $this->row_action_list->child_count() > 0;
    }

    public function set_action_style($style) {
        $this->action_style = $style;
        $this->row_action_list->set_style($style);
    }

    public function action_count() {
        return $this->row_action_list->child_count();
    }

    public function add_row_action($id = "") {
        $row_act = CAction::factory($id);
        $this->row_action_list->add($row_act);
        return $row_act;
    }

    public function set_data_from_treedb($treedb, $parent_id = null) {
        $this->data = $treedb->get_children_data($parent_id);
        return $this;
    }

    public function set_data_from_array($array = array()) {
        $this->data = $array;
        return $this;
    }

    public function set_id_key($id_key) {
        $this->id_key = $id_key;
        return $this;
    }

    public function set_input($input) {
        $this->input = $input;
        return $this;
    }

    public function set_value_key($value_key) {
        $this->value_key = $value_key;
        return $this;
    }

    public function set_applyjs($boolean) {
        $this->applyjs = $boolean;
        return $this;
    }
    
    
    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);

        $html->appendln('<div id="' . $this->id . '" class="dd nestable">')->inc_indent();
        if (count($this->data) > 0) {

            $depth_before = -1;
            $in = 0;
            foreach ($this->data as $d) {
                $depth = $d['depth'];
                if ($depth_before >= $depth) {
                    $html->dec_indent()->appendln('</li>');
                }
                if ($depth_before > $depth) {
                    $range_depth = $depth_before - $depth;
                    for ($i = 0; $i < $range_depth; $i++) {
                        $in--;
                        $html->dec_indent()->appendln('</ol>');
                    }
                }
                if ($depth_before < $depth) {
                    $in++;
                    $html->appendln('<ol class="dd-list">')->inc_indent();
                }
                $html->appendln('<li class="dd-item" data-id="' . $d[$this->id_key] . '">')->inc_indent();

                $html->appendln('<div class="dd-handle">')->inc_indent();
                $val = $d[$this->value_key];

                $new_v = $val;
                if ($this->display_callback !== false && is_callable($this->display_callback)) {
                    $new_v = CDynFunction::factory($this->display_callback)
                            ->add_param($this)
                            ->add_param($d)
                            ->add_param($val)
                            ->set_require($this->requires)
                            ->execute();
                }
                $html->appendln($new_v);

                if ($this->have_action()) {

                    foreach ($d as $k => $v) {
                        $jsparam[$k] = $v;
                    }
                    $jsparam["param1"] = $d[$this->id_key];
                    $this->row_action_list->add_class("pull-right");
                    if ($this->action_style == "btn-dropdown") {
                        $this->row_action_list->add_class("pull-right");
                        if (cobj::get($this, 'bootstrap') == '3') {
                            $this->row_action_list->add_btn_dropdown_class("btn-xs btn-primary");
                        }
                    }
                    $this->row_action_list->regenerate_id(true);
                    $this->row_action_list->apply("jsparam", $jsparam);
                    $this->row_action_list->apply("set_handler_url_param", $jsparam);
                    $this->js_cell.=$this->row_action_list->js();
                    $html->appendln($this->row_action_list->html($html->get_indent()));
                }
                $html->dec_indent()->appendln('</div>');

                $depth_before = $depth;
            }
            for ($i = 0; $i < $in; $i++) {
                $html->dec_indent()->appendln('</li>');
                $html->dec_indent()->appendln('</ol>');
            }
        }
        $html->dec_indent()->appendln('</div>');
        return $html->text();
        
    }

    public function js($indent = 0) {

        $js = new CStringBuilder();
        $js->set_indent($indent);
        if ($this->applyjs) {
            $js->appendln("
			jQuery('#" . $this->id . "').nestable({
				/* config options */
				maxDepth:100
			});
		")->inc_indent();
            if (strlen($this->input) > 0) {
                $js->appendln("
				jQuery('#" . $this->id . "').on('change', function() {
					/* on change event */

					if (window.JSON) {
						jQuery('#" . $this->input . "').val(window.JSON.stringify(jQuery('#" . $this->id . "').nestable('serialize')));//, null, 2));
					} else {
						jQuery('#" . $this->input . "').val('JSON browser support required for this demo.');
					}

				});
				if (window.JSON) {
					jQuery('#" . $this->input . "').val(window.JSON.stringify(jQuery('#" . $this->id . "').nestable('serialize')));//, null, 2));
				} else {
					jQuery('#" . $this->input . "').val('JSON browser support required for this demo.');
				}
			");
            }
        }
        
        if ($this->collapse_all) {
            $js->appendln("
                $('#" . $this->id . "').nestable('collapseAll');
            ");
        }
        
        
        $js->appendln($this->js_cell);
        $js->appendln(parent::js($indent));


        return $js->text();
    }

}
