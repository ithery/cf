<?php

class CNestable extends CElement_Element {

    use CTrait_Compat_Element_Nestable,
        CTrait_Element_ActionList_Row;

    protected $data;
    protected $id_key;
    protected $value_key;
    protected $applyjs;
    protected $input;
    protected $action_style;
    protected $display_callback;
    protected $filter_action_callback_func;
    protected $requires;
    protected $checkbox;
    protected $disable_dnd;
    protected $js_cell;

    public function __construct($id) {
        parent::__construct($id);

        CClientModules::instance()->register_module('jquery.nestable');
        $this->data = array();
        $this->applyjs = true;
        $this->input = '';
        $this->rowActionList = CElement_Factory::createList('ActionList');
        $this->action_style = 'btn-icon-group';
        $this->rowActionList->setStyle('btn-icon-group');
        $this->display_callback = false;
        $this->filter_action_callback_func = "";
        $this->checkbox = false;
        $this->requires = array();
        $this->js_cell = '';
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

    public function filter_action_callback_func($func, $require = "") {
        $this->filter_action_callback_func = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function setDataFromTreeDb($treedb, $parent_id = null) {
        $this->data = $treedb->get_children_data($parent_id);
        return $this;
    }

    public function setDataFromArray($array = array()) {
        $this->data = $array;
        return $this;
    }

    public function set_id_key($id_key) {
        $this->id_key = $id_key;
        return $this;
    }

    public function set_disable_dnd($disable_dnd) {
        $this->disable_dnd = $disable_dnd;
        return $this;
    }

    public function set_have_checkbox($checkbox) {
        $this->checkbox = $checkbox;
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

    public function setApplyJs($boolean) {
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
                if ($this->checkbox) {
                    $html->appendln('<input id="cb_' . $d[$this->id_key] . '" name="cb[' . $d[$this->id_key] . ']" data-parent-id="' . $d["parent_id"] . '" type="checkbox" value="' . $d[$this->id_key] . '"/>')->inc_indent();
                }
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
                $html->dec_indent()->appendln('</div>');
                if ($this->have_action()) {

                    foreach ($d as $k => $v) {
                        $jsparam[$k] = $v;
                    }
                    $jsparam["param1"] = $d[$this->id_key];
                    $this->rowActionList->add_class("pull-right");
                    if ($this->action_style == "btn-dropdown") {
                        $this->rowActionList->add_class("pull-right");
                    }
                    $this->rowActionList->regenerateId(true);
                    $this->rowActionList->apply("jsparam", $jsparam);
                    $this->rowActionList->apply("set_handler_url_param", $jsparam);

                    if (($this->filter_action_callback_func) != null) {
                        $actions = $this->rowActionList->childs();

                        foreach ($actions as $action) {
                            $visibility = CDynFunction::factory($this->filter_action_callback_func)
                                    ->add_param($this)
                                    ->add_param($d)
                                    ->add_param($action)
                                    ->set_require($this->requires)
                                    ->execute();

                            $action->set_visibility($visibility);
                        }
                    }

                    $this->js_cell .= $this->rowActionList->js();
                    $html->appendln($this->rowActionList->html($html->get_indent()));
                }


                $depth_before = $depth;
            }
            for ($i = 0; $i < $in; $i++) {
                $html->dec_indent()->appendln('</li>');
                $html->dec_indent()->appendln('</ol>');
            }
        }
        $html->dec_indent()->appendln('</div>');
        return $html->text();
        /*
          <div class="dd">
          <ol class="dd-list">
          <li class="dd-item" data-id="1">
          <div class="dd-handle">Item 1</div>
          </li>
          <li class="dd-item" data-id="2">
          <div class="dd-handle">Item 2</div>
          </li>
          <li class="dd-item" data-id="3">
          <div class="dd-handle">Item 3</div>
          <ol class="dd-list">
          <li class="dd-item" data-id="4">
          <div class="dd-handle">Item 4</div>
          </li>
          <li class="dd-item" data-id="5">
          <div class="dd-handle">Item 5</div>
          </li>
          </ol>
          </li>
          </ol>
          </div>
         */
    }

    public function js($indent = 0) {


        $js = new CStringBuilder();
        $js->set_indent($indent);
        if ($this->applyjs) {
            if ($this->disable_dnd) {
                $js->appendln("
                    jQuery('#" . $this->id . "').nestable({
                        /* config options */
                        maxDepth:0
                    });
                ")->inc_indent();
            } else {
                $js->appendln("
                    jQuery('#" . $this->id . "').nestable({
                        /* config options */
                        maxDepth:100
                    });
                ")->inc_indent();
            }
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


        $js->append($this->js_cell)->br();

        $js->append($this->js_child($indent))->br();
        return $js->text();
    }

}
