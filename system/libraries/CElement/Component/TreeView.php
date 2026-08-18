<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Renders a jstree-backed tree element. Rendering/behavior is handled client-side by
 * cres.js (see media/js/cres/src/element/component/TreeView), the same way
 * CElement_Component_Calendar works: this class only builds the markup and passes
 * config via the `cres-config` attribute.
 *
 * $nodes starts out as a CElement_Component_TreeView_Node (created in the constructor,
 * representing the tree's root) to fill in directly via getNodes()->addChild()/setChildren(),
 * or via setNodes(array $nodes), for a fixed tree rendered as-is.
 *
 * Calling setNodes(callable $nodes) instead switches to ajax mode: $nodes replaces the
 * property with the callback itself, invoked through the CAjax::TYPE_TREE_VIEW engine
 * (CAjax_Engine_TreeView) with the id of the node jstree is expanding and a fresh
 * CElement_Component_TreeView_Node to fill in via addChild().
 */
class CElement_Component_TreeView extends CElement_Component {
    /**
     * Either a CElement_Component_TreeView_Node (fixed tree, rendered as-is) or a
     * callable (ajax mode, see setNodes()).
     *
     * @var callable|CElement_Component_TreeView_Node
     */
    protected $nodes;

    /**
     * List of file paths to require_once before invoking the callback, ajax mode only.
     *
     * @var array
     */
    protected $requires = [];

    /**
     * @param null|string $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);

        CManager::instance()->registerModule('jstree');
        $this->tag = 'div';
        $this->nodes = new CElement_Component_TreeView_Node('Root');
    }

    /**
     * @param string $id
     *
     * @return \CElement_Component_TreeView
     */
    public static function factory($id = null) {
        return new static($id);
    }

    /**
     * @return callable|CElement_Component_TreeView_Node
     */
    public function getNodes() {
        return $this->nodes;
    }

    /**
     * A callable switches this tree to ajax mode: it's invoked, each time jstree asks for a
     * node's children, as `$nodes($parentId, CElement_Component_TreeView_Node $node)` and
     * should push matching children into $node via addChild(). Anything else is treated as
     * a fixed tree and replaces the current one (see
     * CElement_Component_TreeView_Node::setChildren()).
     *
     * @param callable|array    $nodes
     * @param array|string|null $require one or more file paths to require_once before invoking the callback, ajax mode only
     *
     * @return $this
     */
    public function setNodes($nodes, $require = null) {
        if (is_callable($nodes)) {
            $this->nodes = $nodes;
            if ($require != null) {
                if (!is_array($require)) {
                    $require = [$require];
                }
                foreach ($require as $req) {
                    $this->requires[] = $req;
                }
            }

            return $this;
        }

        if (!($this->nodes instanceof CElement_Component_TreeView_Node)) {
            $this->nodes = new CElement_Component_TreeView_Node('Root');
        }
        $this->nodes->setChildren($nodes);

        return $this;
    }

    /**
     * @return bool
     */
    public function isAjax() {
        return is_callable($this->nodes);
    }

    /**
     * @return string
     */
    public function createAjaxUrl() {
        return CAjax::createMethod()
            ->setType(CAjax::TYPE_TREE_VIEW)
            // jstree's own ajax core.data request doesn't set an explicit HTTP method, so jQuery
            // defaults to GET; must match here or the node id jstree sends is silently dropped.
            ->setMethod('get')
            ->setData('callback', serialize(c::toSerializableClosure($this->nodes)))
            ->setData('requires', $this->requires)
            ->makeUrl();
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();

        $this->addClass('cres:element:component:TreeView');
        $this->setAttr('cres-element', 'component:TreeView');

        $config = ['ajax' => $this->isAjax()];
        if ($this->isAjax()) {
            $config['nodeUrl'] = $this->createAjaxUrl();
        } else {
            $config['data'] = $this->getNodes()->toArray();
        }

        $this->setAttr('cres-config', c::json($config));
    }
}
