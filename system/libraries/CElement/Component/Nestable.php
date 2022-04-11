<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Oct 12, 2018, 1:48:18 PM
 */
class CElement_Component_Nestable extends CElement_Component {
    use CTrait_Compat_Element_Nestable,
        CTrait_Element_ActionList_Row;

    protected $data;

    protected $idKey;

    protected $valueKey;

    protected $applyjs;

    protected $input;

    protected $displayCallbackFunc;

    protected $filterActionCallbackFunc;

    protected $requires;

    protected $checkbox;

    protected $disable_dnd;

    protected $js_cell;

    public function __construct($id) {
        parent::__construct($id);
        CManager::registerModule('jquery.nestable');
        $this->data = [];
        $this->applyjs = true;
        $this->input = '';
        $this->rowActionList = CElement_List_ActionRowList::factory();
        $this->rowActionList->setStyle('btn-icon-group');
        $this->displayCallbackFunc = false;
        $this->filterActionCallbackFunc = '';
        $this->checkbox = false;
        $this->requires = [];
        $this->js_cell = '';
    }

    public static function factory($id) {
        return new CElement_Component_Nestable($id);
    }

    public function displayCallbackFunc($func, $require = '') {
        $this->displayCallbackFunc = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }

        return $this;
    }

    public function filterActionCallbackFunc($func, $require = '') {
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
        /**
         * NOT DONE.
         */
        $orgId = CApp_Base::orgId();

        $root = $root->descendants();
        if (strlen($orgId) > 0) {
            $root = $root->where(function ($query) use ($orgId) {
                $query->where('org_id', '=', $orgId)->orWhereNull('org_id');
            })->where('status', '>', 0);
        }

        $tree = $root->get()->toTree();
        $childArray = [];

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

    public function setDataFromArray($array = []) {
        $this->data = $array;

        return $this;
    }

    public function setIdKey($idKey) {
        $this->idKey = $idKey;

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
        $this->valueKey = $valueKey;

        return $this;
    }

    public function setApplyJs($boolean) {
        $this->applyjs = $boolean;

        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $html->appendln('<div id="' . $this->id . '" class="dd nestable">')->incIndent();
        if (count($this->data) > 0) {
            $depthBefore = -1;
            $in = 0;
            foreach ($this->data as $d) {
                $depth = $d['depth'];
                if ($depthBefore >= $depth) {
                    $html->decIndent()->appendln('</li>');
                }
                if ($depthBefore > $depth) {
                    $rangeDepth = $depthBefore - $depth;
                    for ($i = 0; $i < $rangeDepth; $i++) {
                        $in--;
                        $html->decIndent()->appendln('</ol>');
                    }
                }
                if ($depthBefore < $depth) {
                    $in++;
                    $html->appendln('<ol class="dd-list">')->incIndent();
                }
                $html->appendln('<li class="dd-item" data-id="' . $d[$this->idKey] . '">')->incIndent();

                $html->appendln('<div class="dd-handle">')->incIndent();
                if ($this->checkbox) {
                    $html->appendln('<input id="cb_' . $d[$this->idKey] . '" name="cb[' . $d[$this->idKey] . ']" data-parent-id="' . $d['parent_id'] . '" type="checkbox" value="' . $d[$this->idKey] . '"/>')->incIndent();
                }
                $val = carr::get($d, $this->valueKey);
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
                    $jsparam['param1'] = carr::get($d, $this->idKey);

                    $this->getRowActionList()->regenerateId(true);
                    $this->getRowActionList()->apply('jsparam', $jsparam);
                    $this->getRowActionList()->apply('setHandlerParam', $jsparam);

                    if (($this->filterActionCallbackFunc) != null) {
                        $actions = $this->getRowActionList()->childs();

                        foreach ($actions as $action) {
                            $visibility = CFunction::factory($this->filterActionCallbackFunc)
                                ->addArg($this)
                                ->addArg($d)
                                ->addArg($action)
                                ->setRequire($this->requires)
                                ->execute();
                            if ($visibility == false) {
                                $action->addClass('d-none');
                            }
                            $action->setVisibility($visibility);
                        }
                    }

                    $this->js_cell .= $this->getRowActionList()->js();

                    $html->appendln($this->getRowActionList()->html($html->getIndent()));
                }

                $depthBefore = $depth;
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
        $js->setIndent($indent);
        if ($this->applyjs) {
            if ($this->disable_dnd) {
                $js->appendln("
                    jQuery('#" . $this->id . "').nestable({
                        /* config options */
                        maxDepth:0
                    });
                ")->incIndent();
            } else {
                $js->appendln("
                    jQuery('#" . $this->id . "').nestable({
                        /* config options */
                        maxDepth:100
                    });
                ")->incIndent();
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

        $js->append($this->jsChild($indent))->br();

        return $js->text();
    }
}
