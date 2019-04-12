<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 12, 2018, 1:48:18 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Nestable extends CElement_Component {

    use CTrait_Compat_Element_Nestable,
        CTrait_Element_ActionList_Row;

    protected $data;
    protected $id_key;
    protected $value_key;
    protected $applyjs;
    protected $input;
    protected $action_style;
    protected $displayCallbackFunc;
    protected $filterActionCallbackFunc;
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
        $this->displayCallbackFunc = false;
        $this->filterActionCallbackFunc = "";
        $this->checkbox = false;
        $this->requires = array();
        $this->js_cell = '';
    }

    public static function factory($id) {
        return new CElement_Component_Nestable($id);
    }

    public function displayCallbackFunc($func, $require = "") {
        $this->displayCallbackFunc = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function filterActionCallbackFunc($func, $require = "") {
        $this->filterActionCallbackFunc = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function setDataFromTreeDb(CTreeDB $treedb, $parentId = null) {
        $this->data = $treedb->getChildrenData($parentId);
        return $this;
    }

    public function setDataFromModel(CModel $root) {


        $orgId = CApp_Base::orgId();

        $root = $root->descendants();
        if (strlen($orgId) > 0) {
            $root = $root->where(function($query) use ($orgId) {
                        $query->where('org_id', '=', $orgId)->orWhereNull('org_id');
                    })->where('status', '>', 0);
        }

        $tree = $root->get()->toTree();
        $childArray = array();


        $traverse = function ($nodes) use (&$traverse, &$childArray) {
            foreach ($nodes as $node) {
                if ($node->status == 0) {
                    continue;
                }
                $childArray[] = $node->toArray();
                $traverse($node->getChildren);
            }
        };

        $traverse($tree);


        $this->data = $childArray;
        return $this;
    }

    public function setDataFromArray($array = array()) {
        $this->data = $array;
        return $this;
    }

    public function setIdKey($idKey) {
        $this->id_key = $idKey;
        return $this;
    }

    public function setDisableDnd($disableDnd) {
        $this->disable_dnd = $disableDnd;
        return $this;
    }

    public function setHaveCheckbox($checkbox) {
        $this->checkbox = $checkbox;
        return $this;
    }

    public function setInput($input) {
        $this->input = $input;
        return $this;
    }

    public function setValueKey($valueKey) {
        $this->value_key = $valueKey;
        return $this;
    }

    public function setApplyJs($boolean) {
        $this->applyjs = $boolean;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);

        $html->appendln('<div id="' . $this->id . '" class="dd nestable">')->inc_indent();
        if (count($this->data) > 0) {

            $depth_before = -1;
            $in = 0;
            foreach ($this->data as $d) {

                $depth = $d['depth'];
                if ($depth_before >= $depth) {
                    $html->decIndent()->appendln('</li>');
                }
                if ($depth_before > $depth) {
                    $range_depth = $depth_before - $depth;
                    for ($i = 0; $i < $range_depth; $i++) {
                        $in--;
                        $html->decIndent()->appendln('</ol>');
                    }
                }
                if ($depth_before < $depth) {
                    $in++;
                    $html->appendln('<ol class="dd-list">')->incIndent();
                }
                $html->appendln('<li class="dd-item" data-id="' . $d[$this->id_key] . '">')->inc_indent();

                $html->appendln('<div class="dd-handle">')->incIndent();
                if ($this->checkbox) {
                    $html->appendln('<input id="cb_' . $d[$this->id_key] . '" name="cb[' . $d[$this->id_key] . ']" data-parent-id="' . $d["parent_id"] . '" type="checkbox" value="' . $d[$this->id_key] . '"/>')->inc_indent();
                }
                $val = carr::get($d, $this->value_key);
                $newV = $val;
                if ($this->displayCallbackFunc !== false && is_callable($this->displayCallbackFunc)) {
                    $newV = CFunction::factory($this->displayCallbackFunc)
                            ->addArg($this)
                            ->addArg($d)
                            ->addArg($val)
                            ->setRequire($this->requires)
                            ->execute();
                }
                $html->appendln($newV);
                $html->decIndent()->appendln('</div>');
                if ($this->haveRowAction()) {

                    foreach ($d as $k => $v) {
                        $jsparam[$k] = $v;
                    }
                    $jsparam["param1"] = carr::get($d,$this->id_key);
                    $this->rowActionList->addClass("pull-right");
                    if ($this->action_style == "btn-dropdown") {
                        $this->rowActionList->addClass("pull-right");
                    }
                    $this->rowActionList->regenerateId(true);
                    $this->rowActionList->apply("jsparam", $jsparam);
                    $this->rowActionList->apply("set_handler_url_param", $jsparam);

                    if (($this->filterActionCallbackFunc) != null) {
                        $actions = $this->rowActionList->childs();

                        foreach ($actions as $action) {
                            $visibility = CFunction::factory($this->filterActionCallbackFunc)
                                    ->addArg($this)
                                    ->addArg($d)
                                    ->addArg($action)
                                    ->setRequire($this->requires)
                                    ->execute();

                            $action->setVisibility($visibility);
                        }
                    }

                    $this->js_cell .= $this->rowActionList->js();
                    $html->appendln($this->rowActionList->html($html->get_indent()));
                }


                $depth_before = $depth;
            }
            for ($i = 0; $i < $in; $i++) {
                $html->decIndent()->appendln('</li>');
                $html->decIndent()->appendln('</ol>');
            }
        }
        $html->decIndent()->appendln('</div>');
        return $html->text();
      
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
