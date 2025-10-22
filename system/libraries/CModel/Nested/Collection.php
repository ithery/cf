<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2018, 5:06:51 AM
 */
class CModel_Nested_Collection extends CModel_Collection {
    /**
     * Fill `parent` and `children` relationships for every node in the collection.
     *
     * This will overwrite any previously set relations.
     *
     * @return $this
     */
    public function linkNodes() {
        if ($this->isEmpty()) {
            return $this;
        }
        $groupedNodes = $this->groupBy($this->first()->getParentIdName());
        /* @var NodeTrait|CModel $node */
        foreach ($this->items as $node) {
            if (!$node->getParentId()) {
                $node->setRelation('parent', null);
            }
            $children = $groupedNodes->get($node->getKey(), []);
            /* @var CModel|NodeTrait $child */
            foreach ($children as $child) {
                $child->setRelation('parent', $node);
            }
            $node->setRelation('children', CModel_Collection::make($children));
        }

        return $this;
    }

    /**
     * Build a tree from a list of nodes. Each item will have set children relation.
     *
     * To successfully build tree "id", "lft" and "parent_id" keys must present.
     *
     * If `$root` is provided, the tree will contain only descendants of that node.
     *
     * @param mixed $root
     *
     * @return CModel_Nested_Collection
     */
    public function toTree($root = false) {
        if ($this->isEmpty()) {
            return new static();
        }
        $this->linkNodes();
        $items = [];
        $root = $this->getRootNodeId($root);

        /* @var CModel|NodeTrait $node */
        foreach ($this->items as $node) {
            if ($node->getParentId() == $root) {
                $items[] = $node;
            }
        }

        return new static($items);
    }

    /**
     * @param mixed $root
     *
     * @return int
     */
    protected function getRootNodeId($root = false) {
        if (CModel_Nested_NestedSet::isNode($root)) {
            return $root->getKey();
        }
        if ($root !== false) {
            return $root;
        }
        // If root node is not specified we take parent id of node with
        // least lft value as root node id.
        $leastValue = null;
        /* @var CModel|NodeTrait $node */
        foreach ($this->items as $node) {
            if ($leastValue === null || $node->getLft() < $leastValue) {
                $leastValue = $node->getLft();
                $root = $node->getParentId();
            }
        }

        return $root;
    }

    /**
     * Build a list of nodes that retain the order that they were pulled from
     * the database.
     *
     * @param bool $root
     *
     * @return static
     */
    public function toFlatTree($root = false) {
        $result = new static();
        if ($this->isEmpty()) {
            return $result;
        }
        $groupedNodes = $this->groupBy($this->first()->getParentIdName());

        return $result->flattenTree($groupedNodes, $this->getRootNodeId($root));
    }

    /**
     * Flatten a tree into a non recursive array.
     *
     * @param CModel_Nested_Collection $groupedNodes
     * @param mixed                    $parentId
     *
     * @return $this
     */
    protected function flattenTree(self $groupedNodes, $parentId) {
        foreach ($groupedNodes->get($parentId, []) as $node) {
            $this->push($node);
            $this->flattenTree($groupedNodes, $node->getKey());
        }

        return $this;
    }
}
