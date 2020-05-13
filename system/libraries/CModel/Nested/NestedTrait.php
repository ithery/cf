<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 14, 2019, 6:59:51 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_Nested_NestedTrait {

    /**
     * Pending operation.
     *
     * @var array
     */
    protected $pending;

    /**
     * Whether the node has moved since last save.
     *
     * @var bool
     */
    protected $moved = false;

    /**
     * @var \Carbon\Carbon
     */
    public static $deletedAt;

    /**
     * Keep track of the number of performed operations.
     *
     * @var int
     */
    public static $actionsPerformed = 0;

    /**
     * Sign on model events.
     */
    public static function bootNestedTrait() {

        static::saving(function ($model) {
            $model->callPendingAction();
        });
        static::saved(function ($model) {
            $model->refreshNode();
        });
        static::deleting(function ($model) {
            // We will need fresh data to delete node safely
            $model->refreshNode();
        });

        if (static::usesSoftDelete()) {
            static::restoring(function ($model) {
                static::$deletedAt = $model->{$model->getStatusColumn()};
            });
        }
    }

    /**
     * Set an action.
     *
     * @param string $action
     *
     * @return $this
     */
    protected function setNodeAction($action) {
        $this->pending = func_get_args();
        return $this;
    }

    /**
     * Call pending action.
     */
    protected function callPendingAction() {
        $this->moved = false;
        if (!$this->pending && !$this->exists) {

            $this->makeRoot();
        }
        if (!$this->pending) {
            return;
        }
        $method = 'action' . ucfirst(array_shift($this->pending));

        $parameters = $this->pending;
        $this->pending = null;
        $this->moved = call_user_func_array([$this, $method], $parameters);
    }

    /**
     * @return bool
     */
    protected function actionRaw() {
        return true;
    }

    /**
     * Make a root node.
     */
    protected function actionRoot() {
        // Simplest case that do not affect other nodes.
        if (!$this->exists) {
            $cut = $this->getLowerBound() + 1;
            $this->setLft($cut);
            $this->setRgt($cut + 1);
            return true;
        }
        return $this->insertAt($this->getLowerBound() + 1);
    }

    /**
     * Get the lower bound.
     *
     * @return int
     */
    protected function getLowerBound() {
        return (int) $this->newNestedSetQuery()->max($this->getRgtName());
    }

    /**
     * Append or prepend a node to the parent.
     *
     * @param self $parent
     * @param bool $prepend
     *
     * @return bool
     */
    protected function actionAppendOrPrepend(self $parent, $prepend = false) {
        $parent->refreshNode();

        $cut = $prepend ? $parent->getLft() + 1 : $parent->getRgt();
        if (!$this->insertAt($cut)) {

            return false;
        }
        $this->setDepthWithSubtree();
        $parent->refreshNode();
        return true;
    }

    /**
     * Apply parent model.
     *
     * @param Model|null $value
     *
     * @return $this
     */
    protected function setParent($value) {
        $this->setParentId($value ? $value->getKey() : null)
                ->setRelation('parent', $value);
        $this->setDepth($value ? $value->getDepth() + 1 : 0);
        return $this;
    }

    /**
     * Insert node before or after another node.
     *
     * @param self $node
     * @param bool $after
     *
     * @return bool
     */
    protected function actionBeforeOrAfter(self $node, $after = false) {
        $node->refreshNode();
        return $this->insertAt($after ? $node->getRgt() + 1 : $node->getLft());
    }

    /**
     * Refresh node's crucial attributes.
     */
    public function refreshNode() {
        if (!$this->exists || static::$actionsPerformed === 0) {
            return $this;
        }
        $attributes = $this->newNestedSetQuery()->getNodeData($this->getKey());
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
        //        $this->original = array_merge($this->original, $attributes);
    }

    /**
     * Relation to the parent.
     *
     * @return CModel_Relation_BelongsTo
     */
    public function getParent() {
        return $this->belongsTo(get_class($this), $this->getParentIdName())
                        ->setModel($this);
    }

    /**
     * Relation to children.
     *
     * @return CModel_Relation_HasMany
     */
    public function getChildren() {
        return $this->hasMany(get_class($this), $this->getParentIdName())
                        ->setModel($this);
    }

    /**
     * Get query for descendants of the node.
     *
     * @return CModel_Nested_Relation_Descendants
     */
    public function descendants() {
        return new CModel_Nested_Relation_Descendants($this->newScopedQuery(), $this);
    }

    /**
     *
     * @return CDatabase_Query_Builder
     */
    public function descendantsAndSelf() {
        return $this->newNestedSetQuery()
                        ->where($this->getLftName(), '>=', $this->getLft())
                        ->where($this->getLftName(), '<', $this->getRgt());
    }

    /**
     * Get query for siblings of the node.
     *
     * @return QueryBuilder
     */
    public function siblings() {
        return $this->newScopedQuery()
                        ->where($this->getKeyName(), '<>', $this->getKey())
                        ->where($this->getParentIdName(), '=', $this->getParentId());
    }

    /**
     * Get the node siblings and the node itself.
     *
     * @return \Kalnoy\Nestedset\QueryBuilder
     */
    public function siblingsAndSelf() {
        return $this->newScopedQuery()
                        ->where($this->getParentIdName(), '=', $this->getParentId());
    }

    /**
     * Get query for the node siblings and the node itself.
     *
     * @param  array $columns
     *
     * @return CModel_Collection
     */
    public function getSiblingsAndSelf(array $columns = ['*']) {
        return $this->siblingsAndSelf()->get($columns);
    }

    /**
     * Get query for siblings after the node.
     *
     * @return QueryBuilder
     */
    public function nextSiblings() {
        return $this->nextNodes()
                        ->where($this->getParentIdName(), '=', $this->getParentId());
    }

    /**
     * Get query for siblings before the node.
     *
     * @return QueryBuilder
     */
    public function prevSiblings() {
        return $this->prevNodes()
                        ->where($this->getParentIdName(), '=', $this->getParentId());
    }

    /**
     * Get query for nodes after current node.
     *
     * @return QueryBuilder
     */
    public function nextNodes() {
        return $this->newScopedQuery()
                        ->where($this->getLftName(), '>', $this->getLft());
    }

    /**
     * Get query for nodes before current node in reversed order.
     *
     * @return QueryBuilder
     */
    public function prevNodes() {
        return $this->newScopedQuery()
                        ->where($this->getLftName(), '<', $this->getLft());
    }

    /**
     * Get query ancestors of the node.
     *
     * @return  CModel_Nested_Relation_Ancestors
     */
    public function ancestors() {
        return new CModel_Nested_Relation_Ancestors($this->newScopedQuery(), $this);
    }

    /**
     * Make this node a root node.
     *
     * @return $this
     */
    public function makeRoot() {
        $this->setParent(null)->dirtyBounds();
        return $this->setNodeAction('root');
    }

    /**
     * Save node as root.
     *
     * @return bool
     */
    public function saveAsRoot() {
        if ($this->exists && $this->isRoot()) {
            return $this->save();
        }
        return $this->makeRoot()->save();
    }

    /**
     * Append and save a node.
     *
     * @param self $node
     *
     * @return bool
     */
    public function appendNode(self $node) {
        return $node->appendToNode($this)->save();
    }

    /**
     * Prepend and save a node.
     *
     * @param self $node
     *
     * @return bool
     */
    public function prependNode(self $node) {
        return $node->prependToNode($this)->save();
    }

    /**
     * Append a node to the new parent.
     *
     * @param self $parent
     *
     * @return $this
     */
    public function appendToNode(self $parent) {
        return $this->appendOrPrependTo($parent);
    }

    /**
     * Prepend a node to the new parent.
     *
     * @param self $parent
     *
     * @return $this
     */
    public function prependToNode(self $parent) {
        return $this->appendOrPrependTo($parent, true);
    }

    /**
     * @param self $parent
     * @param bool $prepend
     *
     * @return self
     */
    public function appendOrPrependTo(self $parent, $prepend = false) {
        $this->assertNodeExists($parent)
                ->assertNotDescendant($parent)
                ->assertSameScope($parent);
        $this->setParent($parent)->dirtyBounds();

        return $this->setNodeAction('appendOrPrepend', $parent, $prepend);
    }

    /**
     * Insert self after a node.
     *
     * @param self $node
     *
     * @return $this
     */
    public function afterNode(self $node) {
        return $this->beforeOrAfterNode($node, true);
    }

    /**
     * Insert self before node.
     *
     * @param self $node
     *
     * @return $this
     */
    public function beforeNode(self $node) {
        return $this->beforeOrAfterNode($node);
    }

    /**
     * @param self $node
     * @param bool $after
     *
     * @return self
     */
    public function beforeOrAfterNode(self $node, $after = false) {
        $this->assertNodeExists($node)
                ->assertNotDescendant($node)
                ->assertSameScope($node);
        if (!$this->isSiblingOf($node)) {
            $this->setParent($node->getRelationValue('parent'));
        }
        $this->dirtyBounds();
        return $this->setNodeAction('beforeOrAfter', $node, $after);
    }

    /**
     * Insert self after a node and save.
     *
     * @param self $node
     *
     * @return bool
     */
    public function insertAfterNode(self $node) {
        return $this->afterNode($node)->save();
    }

    /**
     * Insert self before a node and save.
     *
     * @param self $node
     *
     * @return bool
     */
    public function insertBeforeNode(self $node) {
        if (!$this->beforeNode($node)->save())
            return false;
        // We'll update the target node since it will be moved
        $node->refreshNode();
        return true;
    }

    /**
     * @param $lft
     * @param $rgt
     * @param $parentId
     *
     * @return $this
     */
    public function rawNode($lft, $rgt, $parentId, $depth = null) {


        if ($depth == null) {
            $depth = 0;
            $parentModel = static::find($parentId);
            if ($parentModel != null) {
                $depth = $parentModel->getDepth() + 1;
            }
        }

        $this->setLft($lft)->setRgt($rgt)->setDepth($depth)->setParentId($parentId);
        return $this->setNodeAction('raw');
    }

    /**
     * Move node up given amount of positions.
     *
     * @param int $amount
     *
     * @return bool
     */
    public function up($amount = 1) {
        $sibling = $this->prevSiblings()
                ->defaultOrder('desc')
                ->skip($amount - 1)
                ->first();
        if (!$sibling)
            return false;
        return $this->insertBeforeNode($sibling);
    }

    /**
     * Move node down given amount of positions.
     *
     * @param int $amount
     *
     * @return bool
     */
    public function down($amount = 1) {
        $sibling = $this->nextSiblings()
                ->defaultOrder()
                ->skip($amount - 1)
                ->first();
        if (!$sibling)
            return false;
        return $this->insertAfterNode($sibling);
    }

    /**
     * Insert node at specific position.
     *
     * @param  int $position
     *
     * @return bool
     */
    protected function insertAt($position) {
        ++static::$actionsPerformed;
        $result = $this->exists ? $this->moveNode($position) : $this->insertNode($position);
        return $result;
    }

    /**
     * Move a node to the new position.
     *
     * @since 2.0
     *
     * @param int $position
     *
     * @return int
     */
    protected function moveNode($position) {
        $updated = $this->newNestedSetQuery()
                ->moveNode($this->getKey(), $position);

        if ($updated instanceof CDatabase_Driver_Mysqli_Result) {
            $updated = $updated->count();
        }
        if ($updated) {
            $this->refreshNode();
        }
        return $updated;
    }

    /**
     * Insert new node at specified position.
     *
     * @since 2.0
     *
     * @param int $position
     *
     * @return bool
     */
    protected function insertNode($position) {
        $this->newNestedSetQuery()->makeGap($position, 2);
        $height = $this->getNodeHeight();
        $this->setLft($position);
        $this->setRgt($position + $height - 1);
        return true;
    }

    /**
     * Update the tree when the node is removed physically.
     */
    protected function deleteDescendants() {
        $lft = $this->getLft();
        $rgt = $this->getRgt();
        $method = $this->usesSoftDelete() && $this->forceDeleting ? 'forceDelete' : 'delete';
        $this->descendants()->{$method}();
        if ($this->hardDeleting()) {
            $height = $rgt - $lft + 1;
            $this->newNestedSetQuery()->makeGap($rgt + 1, -$height);
            // In case if user wants to re-create the node
            $this->makeRoot();
            static::$actionsPerformed++;
        }
    }

    /**
     * Restore the descendants.
     *
     * @param $deletedAt
     */
    protected function restoreDescendants($deletedAt) {
        $this->descendants()
                ->where($this->getDeletedAtColumn(), '>=', $deletedAt)
                ->restore();
    }

    /**
     * {@inheritdoc}
     *
     * @since 2.0
     */
    public function newModelBuilder($query) {
        return new CModel_Nested_Query($query);
    }

    /**
     * Get a new base query that includes deleted nodes.
     *
     * @since 1.1
     *
     * @return QueryBuilder
     */
    public function newNestedSetQuery($table = null) {
        $builder = $this->usesSoftDelete() ? $this->withTrashed() : $this->newQuery();
        return $this->applyNestedSetScope($builder, $table);
    }

    /**
     * @param string $table
     *
     * @return CDatabase_Query_Builder
     */
    public function newScopedQuery($table = null) {
        return $this->applyNestedSetScope($this->newQuery(), $table);
    }

    /**
     * @param mixed $query
     * @param string $table
     *
     * @return mixed
     */
    public function applyNestedSetScope($query, $table = null) {
        if (!$scoped = $this->getScopeAttributes()) {
            return $query;
        }
        if (!$table) {
            $table = $this->getTable();
        }
        foreach ($scoped as $attribute) {
            $query->where($table . '.' . $attribute, '=', $this->getAttributeValue($attribute));
        }
        return $query;
    }

    /**
     * @return array
     */
    protected function getScopeAttributes() {
        return null;
    }

    /**
     * @param array $attributes
     *
     * @return self
     */
    public static function scoped(array $attributes) {
        $instance = new static;
        $instance->setRawAttributes($attributes);
        return $instance->newScopedQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function newCollection(array $models = array()) {
        return new CModel_Nested_Collection($models);
    }

    /**
     * {@inheritdoc}
     *
     * Use `children` key on `$attributes` to create child nodes.
     *
     * @param self $parent
     */
    public static function create(array $attributes = [], self $parent = null) {
        $children = array_pull($attributes, 'children');
        $instance = new static($attributes);
        if ($parent) {
            $instance->appendToNode($parent);
        }
        $instance->save();
        // Now create children
        $relation = new CModel_Collection;
        foreach ((array) $children as $child) {
            $relation->add($child = static::create($child, $instance));
            $child->setRelation('parent', $instance);
        }
        $instance->refreshNode();
        return $instance->setRelation('children', $relation);
    }

    /**
     * Get node height (rgt - lft + 1).
     *
     * @return int
     */
    public function getNodeHeight() {
        if (!$this->exists) {
            return 2;
        }
        return $this->getRgt() - $this->getLft() + 1;
    }

    /**
     * Get number of descendant nodes.
     *
     * @return int
     */
    public function getDescendantCount() {
        return ceil($this->getNodeHeight() / 2) - 1;
    }

    /**
     * Set the value of model's parent id key.
     *
     * Behind the scenes node is appended to found parent node.
     *
     * @param int $value
     *
     * @throws Exception If parent node doesn't exists
     */
    public function setParentIdAttribute($value) {
        if ($this->getParentId() == $value) {
            return;
        }
        if ($value) {
            $this->appendToNode($this->newScopedQuery()->findOrFail($value));
        } else {
            $this->makeRoot();
        }
    }

    /**
     * Get whether node is root.
     *
     * @return boolean
     */
    public function isRoot() {
        return is_null($this->getParentId());
    }

    /**
     * @return bool
     */
    public function isLeaf() {
        return $this->getLft() + 1 == $this->getRgt();
    }

    /**
     * Returns true if this is a trunk node (not root or leaf).
     *
     * @return boolean
     */
    public function isTrunk() {
        return !$this->isRoot() && !$this->isLeaf();
    }

    /**
     * Returns true if this is a child node.
     *
     * @return boolean
     */
    public function isChild() {
        return !$this->isRoot();
    }

    /**
     * Get the lft key name.
     *
     * @return  string
     */
    public function getLftName() {
        return CModel_Nested_NestedSet::LFT;
    }

    /**
     * Get the rgt key name.
     *
     * @return  string
     */
    public function getRgtName() {
        return CModel_Nested_NestedSet::RGT;
    }

    /**
     * Get the depth key name.
     *
     * @return  string
     */
    public function getDepthName() {
        return CModel_Nested_NestedSet::DEPTH;
    }

    /**
     * Get the parent id key name.
     *
     * @return  string
     */
    public function getParentIdName() {
        return CModel_Nested_NestedSet::PARENT_ID;
    }

    /**
     * Get the value of the model's lft key.
     *
     * @return  integer
     */
    public function getLft() {
        return $this->getAttributeValue($this->getLftName());
    }

    /**
     * Get the value of the model's rgt key.
     *
     * @return  integer
     */
    public function getRgt() {
        return $this->getAttributeValue($this->getRgtName());
    }

    /**
     * Get the value of the model's deoth key.
     *
     * @return  integer
     */
    public function getDepth() {
        return $this->getAttributeValue($this->getDepthName());
    }

    /**
     * Get the value of the model's parent id key.
     *
     * @return  integer
     */
    public function getParentId() {
        return $this->getAttributeValue($this->getParentIdName());
    }

    /**
     * Returns node that is next to current node without constraining to siblings.
     *
     * This can be either a next sibling or a next sibling of the parent node.
     *
     * @param array $columns
     *
     * @return self
     */
    public function getNextNode(array $columns = ['*']) {
        return $this->nextNodes()->defaultOrder()->first($columns);
    }

    /**
     * Returns node that is before current node without constraining to siblings.
     *
     * This can be either a prev sibling or parent node.
     *
     * @param array $columns
     *
     * @return self
     */
    public function getPrevNode(array $columns = ['*']) {
        return $this->prevNodes()->defaultOrder('desc')->first($columns);
    }

    /**
     * @param array $columns
     *
     * @return Collection
     */
    public function getAncestors(array $columns = ['*']) {
        return $this->ancestors()->get($columns);
    }

    /**
     * @param array $columns
     *
     * @return Collection|self[]
     */
    public function getDescendants(array $columns = ['*']) {
        return $this->descendants()->get($columns);
    }

    /**
     * @param array $columns
     *
     * @return Collection|self[]
     */
    public function getSiblings(array $columns = ['*']) {
        return $this->siblings()->get($columns);
    }

    /**
     * @param array $columns
     *
     * @return Collection|self[]
     */
    public function getNextSiblings(array $columns = ['*']) {
        return $this->nextSiblings()->get($columns);
    }

    /**
     * @param array $columns
     *
     * @return Collection|self[]
     */
    public function getPrevSiblings(array $columns = ['*']) {
        return $this->prevSiblings()->get($columns);
    }

    /**
     * @param array $columns
     *
     * @return self
     */
    public function getNextSibling(array $columns = ['*']) {
        return $this->nextSiblings()->defaultOrder()->first($columns);
    }

    /**
     * @param array $columns
     *
     * @return self
     */
    public function getPrevSibling(array $columns = ['*']) {
        return $this->prevSiblings()->defaultOrder('desc')->first($columns);
    }

    /**
     * Get whether a node is a descendant of other node.
     *
     * @param self $other
     *
     * @return bool
     */
    public function isDescendantOf(self $other) {
        return $this->getLft() > $other->getLft() &&
                $this->getLft() < $other->getRgt();
    }

    /**
     * Get whether a node is itself or a descendant of other node.
     *
     * @param self $other
     *
     * @return bool
     */
    public function isSelfOrDescendantOf(self $other) {
        return $this->getLft() >= $other->getLft() &&
                $this->getLft() < $other->getRgt();
    }

    /**
     * Get whether the node is immediate children of other node.
     *
     * @param self $other
     *
     * @return bool
     */
    public function isChildOf(self $other) {
        return $this->getParentId() == $other->getKey();
    }

    /**
     * Get whether the node is a sibling of another node.
     *
     * @param self $other
     *
     * @return bool
     */
    public function isSiblingOf(self $other) {
        return $this->getParentId() == $other->getParentId();
    }

    /**
     * Get whether the node is an ancestor of other node, including immediate parent.
     *
     * @param self $other
     *
     * @return bool
     */
    public function isAncestorOf(self $other) {
        return $other->isDescendantOf($this);
    }

    /**
     * Get whether the node is itself or an ancestor of other node, including immediate parent.
     *
     * @param self $other
     *
     * @return bool
     */
    public function isSelfOrAncestorOf(self $other) {
        return $other->isSelfOrDescendantOf($this);
    }

    /**
     * Get whether the node has moved since last save.
     *
     * @return bool
     */
    public function hasMoved() {
        return $this->moved;
    }

    /**
     * @return array
     */
    protected function getArrayableRelations() {
        $result = parent::getArrayableRelations();
        // To fix #17 when converting tree to json falling to infinite recursion.
        unset($result['parent']);
        return $result;
    }

    /**
     * Get whether user is intended to delete the model from database entirely.
     *
     * @return bool
     */
    protected function hardDeleting() {
        return !$this->usesSoftDelete() || $this->forceDeleting;
    }

    /**
     * @return array
     */
    public function getBounds() {
        return [$this->getLft(), $this->getRgt()];
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setLft($value) {
        $this->attributes[$this->getLftName()] = $value;
        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setRgt($value) {
        $this->attributes[$this->getRgtName()] = $value;
        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setDepth($value = null) {

        $this->refreshNode();

        $level = $value === null ? $this->getLevel() : $value;

        $this->newNestedSetQuery()->where($this->getKeyName(), '=', $this->getKey())->update(array($this->getDepthName() => $level));
        $this->setAttribute($this->getDepthName(), $level);


        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setParentId($value) {
        $this->attributes[$this->getParentIdName()] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    protected function dirtyBounds() {
        $this->original[$this->getLftName()] = null;
        $this->original[$this->getRgtName()] = null;
        $this->original[$this->getDepthName()] = null;
        return $this;
    }

    /**
     * @param self $node
     *
     * @return $this
     */
    protected function assertNotDescendant(self $node) {
        if ($node == $this || $node->isDescendantOf($this)) {
            $str = "this lft:" . $this->getLft() . ', this rgt:' . $this->getRgt() . ' other lft:' . $node->getLft() . ' other rgt:' . $node->getRgt();
            $str .= "-- this id" . $this->getKey() . ',other id:' . $node->getKey();
            throw new LogicException('Node must not be a descendant.' . $str);
        }
        return $this;
    }

    /**
     * @param self $node
     *
     * @return $this
     */
    protected function assertNodeExists(self $node) {
        if (!$node->getLft() || !$node->getRgt()) {
            throw new LogicException('Node must exists.');
        }
        return $this;
    }

    /**
     * @param self $node
     */
    protected function assertSameScope(self $node) {
        if (!$scoped = $this->getScopeAttributes()) {
            return;
        }
        foreach ($scoped as $attr) {
            if ($this->getAttribute($attr) != $node->getAttribute($attr)) {
                throw new LogicException('Nodes must be in the same scope');
            }
        }
    }

    /**
     * @param array|null $except
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function replicate(array $except = null) {
        $defaults = [
            $this->getParentIdName(),
            $this->getLftName(),
            $this->getRgtName(),
        ];
        $except = $except ? array_unique(array_merge($except, $defaults)) : $defaults;
        return parent::replicate($except);
    }

    /**
     * Returns the level of this node in the tree.
     * Root level is 0.
     *
     * @return int
     */
    public function getLevel() {
        if (is_null($this->getParentId())) {
            return 0;
        }

        return $this->computeLevel();
    }

    /**
     * Compute current node level. If could not move past ourseleves return
     * our ancestor count, otherwhise get the first parent level + the computed
     * nesting.
     *
     * @return integer
     */
    protected function computeLevel() {
        list($node, $nesting) = $this->determineDepth($this);

        if ($node->equals($this)) {
            return $this->ancestors()->count();
        }
        return $node->getLevel() + $nesting;
    }

    /**
     * Return an array with the last node we could reach and its nesting level
     *
     * @param   Baum\Node $node
     * @param   integer   $nesting
     * @return  array
     */
    protected function determineDepth($node, $nesting = 0) {
        // Traverse back up the ancestry chain and add to the nesting level count
        while ($parent = $node->getParent()->first()) {
            $nesting = $nesting + 1;

            $node = $parent;
        }

        return array($node, $nesting);
    }

    /**
     * Equals?
     *
     * @param \Baum\Node
     * @return boolean
     */
    public function equals($node) {
        return ($this == $node);
    }

    /**
     * Sets the depth attribute for the current node and all of its descendants.
     *
     * @return $this
     */
    public function setDepthWithSubtree() {
        $self = $this;


        $self->refreshNode();

        $self->descendantsAndSelf()->select($self->getKeyName())->lockForUpdate()->get();

        $oldDepth = !is_null($self->getDepth()) ? $self->getDepth() : 0;

        $newDepth = $self->getLevel();

        $self->newNestedSetQuery()->where($self->getKeyName(), '=', $self->getKey())->update(array($self->getDepthName() => $newDepth));
        $self->setAttribute($self->getDepthName(), $newDepth);

        $diff = $newDepth - $oldDepth;

        if ($this->parent_id == 195) {
            cdbg::dd($newDepth);
        }
        if ($diff > 0) {

            cdbg::dd($diff);
        }
        if (!$self->isLeaf() && $diff != 0) {
            $self->descendants()->increment($self->getDepthName(), $diff);
        }

        return $this;
    }

    /**
     * Extracts current node (self) from current query expression.
     *
     * @return CDatabase_Query_builder
     */
    public function scopeWithoutSelf($query) {
        return $this->scopeWithoutNode($query, $this);
    }

    /**
     * Extracts first root (from the current node p-o-v) from current query
     * expression.
     *
     * @return CDatabase_Query_builder
     */
    public function scopeWithoutRoot($query) {
        return $this->scopeWithoutNode($query, $this->getRoot());
    }

    /**
     * Query scope which extracts a certain node object from the current query
     * expression.
     *
     * @return CDatabase_Query_builder
     */
    public function scopeWithoutNode($query, $node) {
        return $query->where($node->getKeyName(), '!=', $node->getKey());
    }

    /**
     * Instance scope which targes all the ancestor chain nodes including
     * the current one.
     *
     * @return CDatabase_Query_builder
     */
    public function ancestorsAndSelf() {
        return $this->newNestedSetQuery()
                        ->where($this->getLftName(), '<=', $this->getLft())
                        ->where($this->getRgtName(), '>=', $this->getRgt());
    }

    /**
     * Returns the root node starting at the current node.
     *
     * @return CModel
     */
    public function getRoot() {
        if ($this->exists) {
            return $this->ancestorsAndSelf()->whereNull($this->getParentIdName())->first();
        } else {
            $parentId = $this->getParentId();

            if (!is_null($parentId) && $currentParent = static::find($parentId)) {
                return $currentParent->getRoot();
            } else {
                return $this;
            }
        }
    }

    /**
     * Provides a depth level limit for the query.
     *
     * @param   query   CDatabase_Query_Builder
     * @param   limit   integer
     * @return  CDatabase_Query_Builder
     */
    public function scopeLimitDepth($query, $limit) {
        $depth = $this->exists ? $this->getDepth() : $this->getLevel();
        $max = $depth + $limit;
        $scopes = array($depth, $max);

        return $query->whereBetween($this->getDepthName(), array(min($scopes), max($scopes)));
    }

}
