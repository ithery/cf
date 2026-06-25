<?php

/**
 * Dynamic repeatable form rows with add/remove functionality.
 *
 * @see CElement_Component
 */
class CElement_Component_Repeater extends CElement_Component {
    /**
     * @var null|callable
     */
    protected $itemBuilder;

    /**
     * @var bool
     */
    protected $canDelete;

    /**
     * @var bool
     */
    protected $canAdd;

    /**
     * @var string
     */
    protected $addLabel;

    /**
     * @var string
     */
    protected $deleteLabel;

    /**
     * @var string
     */
    protected $addButtonClass;

    /**
     * @var string
     */
    protected $deleteButtonClass;

    /**
     * @var int
     */
    protected $minItem;

    /**
     * @var null|int
     */
    protected $maxItem;

    /**
     * @param null|string $id
     */
    public function __construct($id = null) {
        parent::__construct($id);
        $this->canDelete = true;
        $this->canAdd = true;
        $this->deleteLabel = 'Delete';
        $this->addLabel = 'New Item';
        $this->addButtonClass = 'btn-primary w-100';
        $this->deleteButtonClass = 'btn-danger';
        $this->minItem = 1;
        $this->maxItem = null;
    }

    /**
     * @param null|string $id
     *
     * @return static
     */
    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
    }

    /**
     * @param callable $itemBuilder
     *
     * @return $this
     */
    public function setItemBuilder($itemBuilder) {
        $this->itemBuilder = $itemBuilder;

        return $this;
    }

    /**
     * @param int $minItem
     *
     * @return $this
     */
    public function setMinItem($minItem) {
        $this->minItem = (int) $minItem;

        return $this;
    }

    /**
     * @param int $maxItem
     *
     * @return $this
     */
    public function setMaxItem($maxItem) {
        $this->maxItem = (int) $maxItem;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setCanAdd($bool) {
        $this->canAdd = (bool) $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setCanDelete($bool) {
        $this->canDelete = (bool) $bool;

        return $this;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setAddLabel($label) {
        $this->addLabel = $label;

        return $this;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setDeleteLabel($label) {
        $this->deleteLabel = $label;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setAddButtonClass($class) {
        $this->addButtonClass = $class;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setDeleteButtonClass($class) {
        $this->deleteButtonClass = $class;

        return $this;
    }

    /**
     * @return void
     */
    protected function build() {
        $config = [
            'minItem' => $this->minItem,
            'maxItem' => $this->maxItem,
        ];
        $this->addClass('cres:element:component:Repeater');
        $this->setAttr('cres-element', 'component:Repeater');
        $this->setAttr('cres-config', c::json($config));
        $divItems = $this->addDiv()->addClass('cres-repeater-wrapper');
        if ($this->itemBuilder != null) {
            $divRow = $divItems->addDiv()->addClass('cres-repeater-row');
            $divItem = $divRow->addDiv()->addClass('cres-repeater-item');
            call_user_func_array($this->itemBuilder, [$divItem]);
            if ($this->canDelete) {
                $divAction = $divRow->addDiv()->addClass('cres-repeater-item-action');
                $divAction->addAction()->setLabel($this->deleteLabel)->addClass($this->deleteButtonClass . ' cres-repeater-action-delete');
            }
        }
        if ($this->canAdd) {
            $divAction = $this->addDiv()->addClass('cres-repeater-action');
            $divAction->addAction()->setLabel($this->addLabel)->addClass($this->addButtonClass . ' cres-repeater-action-add');
        }
    }
}
