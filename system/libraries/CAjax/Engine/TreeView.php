<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Ajax engine backing CElement_Component_TreeView's ajax mode: requires the files
 * registered via CElement_Component_TreeView::setNodes()'s $require argument, then
 * invokes that callback with ($parentId, CElement_Component_TreeView_Node), returning
 * the children the callback pushed into it as JSON (jstree's expected node-list shape).
 */
class CAjax_Engine_TreeView extends CAjax_Engine {
    /**
     * @return CHTTP_JsonResponse
     */
    public function execute() {
        $data = $this->getData();
        $input = $this->getInput();

        foreach (carr::get($data, 'requires', []) as $require) {
            if (file_exists($require)) {
                require_once $require;
            }
        }

        $callback = unserialize(carr::get($data, 'callback'));

        $node = new CElement_Component_TreeView_Node('Root');
        c::call($callback, [carr::get($input, 'id'), $node]);

        return c::response()->json($node->toArray());
    }
}
