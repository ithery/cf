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

    protected $displayCallback;

    protected $filterActionCallbackFunc;

    protected $requires;

    protected $checkbox;

    protected $disableDnd;

    protected $js_cell;

    protected $isCollapsed = false;

    protected $query;

    public function __construct($id) {
        parent::__construct($id);
        CManager::registerModule('jquery.nestable');
        $this->data = [];
        $this->applyjs = true;
        $this->input = '';
        $this->rowActionList = CElement_List_ActionRowList::factory();
        $this->rowActionList->setStyle('btn-icon-group');
        $this->displayCallback = false;
        $this->filterActionCallbackFunc = '';
        $this->checkbox = false;
        $this->requires = [];
        $this->js_cell = '';
        $this->disableDnd = false;
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Nestable
     */
    public static function factory($id) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    public function setDisplayCallback($func, $require = '') {
        $this->displayCallback = $func;
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

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setCollapsed($bool = true) {
        $this->isCollapsed = $bool;

        return $this;
    }

    public function setDataFromModel($model, $queryCallback = null) {
        if (is_string($model)) {
            $this->query = CManager::createModelDataProvider($model, $queryCallback);

            return $this;
        }
        /**
         * NOT DONE.
         */
        $orgId = CApp_Base::orgId();

        $root = $model->descendants();
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
        $this->disableDnd = $disableDnd;

        return $this;
    }

    public function disableDnd() {
        $this->disableDnd = true;

        return $this;
    }

    public function enableDnd() {
        $this->disableDnd = false;

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

    protected function getDataFromQuery() {
        $models = $this->query->toEnumerable();
        $childArray = [];
        if ($models instanceof CModel_Nested_Collection) {
            $tree = $models->toTree();

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
        }

        $this->data = $childArray;
    }

    public function html($indent = 0) {
        if ($this->query != null) {
            $this->getDataFromQuery();
        }
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $styles = '';
        // $styles = $this->disableDnd ? 'pointer-events: none;' : '';
        $html->appendln('<div id="' . $this->id . '" cres-element="component:Nestable" class="dd nestable cres-nestable cres:element:component:Nestable" style="' . $styles . '">')->incIndent();
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
                $itemClass = '';
                if ($this->disableDnd) {
                    $itemClass = ' dd-nodrag';
                }
                $html->appendln('<li class="dd-item ' . $itemClass . '" data-id="' . $d[$this->idKey] . '">')->incIndent();

                $html->appendln('<div class="dd-handle">')->incIndent();
                if ($this->checkbox) {
                    $html->appendln('<input id="cb_' . $d[$this->idKey] . '" name="cb[' . $d[$this->idKey] . ']" data-parent-id="' . $d['parent_id'] . '" type="checkbox" value="' . $d[$this->idKey] . '"/>')->incIndent();
                }
                $val = carr::get($d, $this->valueKey, null);
                $newV = $val;
                if ($this->displayCallback !== false && is_callable($this->displayCallback)) {
                    $newV = CFunction::factory($this->displayCallback)
                        ->addArg($this)
                        ->addArg($d)
                        ->addArg($val)
                        ->setRequire($this->requires)
                        ->execute();
                }

                if ($newV instanceof CRenderable) {
                    $html->appendln($newV->html());
                    $this->js_cell .= $newV->js();
                } else {
                    $html->appendln($newV);
                }

                $html->decIndent()->appendln('</div>');
                if ($this->haveRowAction()) {
                    $this->js_cell .= $this->drawActionAndGetJs($html, $d, $this->idKey);
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
            $js->appendln("
                jQuery('#" . $this->id . "').nestable({
                    maxDepth:100
                });
            ")->incIndent();

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
            if ($this->isCollapsed) {
                $js->appendln("
                    jQuery('#" . $this->id . "').nestable('collapseAll');
                ");
            }
        }

        $js->append($this->js_cell)->br();

        $js->append($this->jsChild($indent))->br();

        return $js->text();
    }

    protected function drawActionAndGetJs(CStringBuilder $html, array $row, $key) {
        $js = '';
        if ($this->haveRowAction()) {
            $html->appendln('<td class="low-padding align-center cell-action td-action">')->incIndent()->br();
            foreach ($row as $k => $v) {
                $jsparam[$k] = $v;
            }

            $jsparam['param1'] = $key;

            $this->getRowActionList()->regenerateId(true);
            $this->getRowActionList()->apply('setJsParam', $jsparam);
            $this->getRowActionList()->apply('setHandlerParam', $jsparam);
            $actions = $this->getRowActionList()->childs();

            foreach ($actions as &$action) {
                if (($this->filterActionCallbackFunc) != null) {
                    $visibility = CFunction::factory($this->filterActionCallbackFunc)
                        ->addArg($this)
                        ->addArg('action')
                        ->addArg($row)
                        ->addArg($action)
                        ->setRequire($this->requires)
                        ->execute();
                    if ($visibility == false) {
                        $action->addClass('d-none');
                    }
                    $action->setVisibility($visibility);
                }
                if ($action instanceof CElement_Component_ActionRow) {
                    $action->applyRowCallback($row);
                }
            }

            $js = $this->getRowActionList()->js();

            $html->appendln($this->getRowActionList()->html($html->getIndent()));
            $html->decIndent()->appendln('</td>')->br();
        }

        return $js;
    }
}
