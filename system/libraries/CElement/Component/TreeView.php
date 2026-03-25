<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_TreeView extends CElement_Component {
    protected $node;

    public function __construct($id = null) {
        parent::__construct($id);
        CManager::instance()->registerModule('jstree');
        $this->node = new CElement_Component_TreeView_Node('Root');
        $this->tag = 'div';
    }

    public function setData($data) {
        $this->node->clear();
        foreach ($data as $d) {
            $this->node->addChild($d);
        }
    }

    protected function jsonData() {
        return json_encode($this->node->toArray());
    }

    public function js($indent = 0) {
        return "
            $('#" . $this->id . "').jstree({
                'core' : {
                    'data' : " . $this->jsonData() . '
                }

            });
        ';
    }
}
