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
     * @var int
     */
    protected $minItem;

    /**
     * @param null|string $id
     */
    public function __construct($id = null) {
        parent::__construct($id);
        $this->canDelete = true;
        $this->canAdd = true;
        $this->deleteLabel = 'Delete';
        $this->addLabel = 'New Item';
        $this->minItem = 1;
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
     * @return void
     */
    protected function build() {
        $config = [
            'minItem' => $this->minItem,
        ];
        $this->addClass('cres:element:component:Repeater');
        $this->setAttr('cres-element', 'component:Repeater');
        $this->setAttr('cres-config', c::json($config));
        $divItems = $this->addDiv()->addClass('cres-repeater-wrapper');
        if ($this->itemBuilder != null) {
            $divRow = $divItems->addDiv()->addClass('cres-repeater-row');
            $divItem = $divRow->addDiv()->addClass('cres-repeater-item');
            call_user_func_array($this->itemBuilder, [$divItem]);
            $divAction = $divRow->addDiv()->addClass('cres-repeater-item-action');
            $divAction->addAction()->setLabel($this->deleteLabel)->addClass('btn-danger cres-repeater-action-delete');
        }
        $divAction = $this->addDiv()->addClass('cres-repeater-action');
        $divAction->addAction()->setLabel($this->addLabel)->addClass('btn-primary w-100 cres-repeater-action-add');
    }
}
