<?php

use Illuminate\Contracts\Support\Arrayable;

defined('SYSPATH') or die('No direct access allowed.');

/**
 * A single jstree node: a label, an optional icon, and child nodes. Also doubles as the
 * mutable "collection" a CElement_Component_TreeView populates, directly (getNodes()) or,
 * in ajax mode, via the CElement_Component_TreeView::setNodes() callback (see
 * CAjax_Engine_TreeView) -- the same way CElement_Component_Calendar_CalendarEvents works
 * for CElement_Component_Calendar.
 */
class CElement_Component_TreeView_Node implements Arrayable {
    /**
     * @var null|int|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var null|string
     */
    protected $icon;

    /**
     * @var CElement_Component_TreeView_Node[]
     */
    protected $children = [];

    /**
     * Overrides whether jstree renders an expand arrow for this node, for ajax mode: null lets
     * it fall through to "does it already have children added", true/false forces it either
     * way so a not-yet-loaded child level can still show as expandable. See setHasChildren().
     *
     * @var null|bool
     */
    protected $hasChildren;

    /**
     * @param array $array shape: ['id' => null|int|string, 'text' => string, 'icon' => null|string,
     *                     'children' => array, 'hasChildren' => null|bool]
     *
     * @return static
     */
    public static function createFromArray($array) {
        $text = carr::get($array, 'text', '');
        $icon = carr::get($array, 'icon');
        $children = carr::get($array, 'children', []);

        /** @phpstan-ignore-next-line */
        $node = new static($text, $children, $icon);
        $node->setId(carr::get($array, 'id'));
        $node->setHasChildren(carr::get($array, 'hasChildren'));

        return $node;
    }

    /**
     * @param string      $text
     * @param array       $children list of CElement_Component_TreeView_Node|array|string, see addChild()
     * @param null|string $icon
     *
     * @return void
     */
    public function __construct($text, $children = [], $icon = null) {
        $this->text = $text;
        $this->icon = $icon;

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * A node instance, an array (see createFromArray()), or a plain label string.
     *
     * @param CElement_Component_TreeView_Node|array|string $child
     *
     * @throws Exception
     *
     * @return $this
     */
    public function addChild($child) {
        if (!($child instanceof CElement_Component_TreeView_Node)) {
            if (is_array($child)) {
                $child = static::createFromArray($child);
            } elseif (is_string($child)) {
                /** @phpstan-ignore-next-line */
                $child = new static($child);
            } else {
                throw new Exception('child is on bad format');
            }
        }
        $this->children[] = $child;

        return $this;
    }

    /**
     * Replace the whole child list.
     *
     * @param array $children list of CElement_Component_TreeView_Node|array|string, see addChild()
     *
     * @return $this
     */
    public function setChildren(array $children) {
        $this->clear();
        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * @return CElement_Component_TreeView_Node[]
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @param null|string $icon
     *
     * @return $this
     */
    public function setIcon($icon) {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return null|int|string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param null|int|string $id
     *
     * @return $this
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|bool
     */
    public function getHasChildren() {
        return $this->hasChildren;
    }

    /**
     * Force this node to render as expandable (or not) in ajax mode, regardless of whether
     * its children have been loaded yet -- lets a lazily-loaded node still show an expand
     * arrow before its children are fetched.
     *
     * @param null|bool $hasChildren
     *
     * @return $this
     */
    public function setHasChildren($hasChildren) {
        $this->hasChildren = $hasChildren;

        return $this;
    }

    /**
     * @return $this
     */
    public function clear() {
        $this->children = [];

        return $this;
    }

    /**
     * Get this node's children as a jstree-shaped array (not including this node itself).
     * Each entry's `children` is either the actual nested array, or -- when a child has none
     * added yet but was marked via setHasChildren(true) -- the boolean `true`, telling jstree
     * to show it as expandable and lazy-load its children on demand.
     *
     * @return array
     */
    public function toArray() {
        return c::collect($this->children)->map(function ($node) {
            $children = $node->getChildren();
            $array = [
                'text' => $node->getText(),
                'icon' => $node->getIcon(),
                'children' => $children ? $node->toArray() : (bool) $node->getHasChildren(),
            ];
            if ($node->getId() !== null) {
                $array['id'] = $node->getId();
            }

            return $array;
        })->all();
    }
}
