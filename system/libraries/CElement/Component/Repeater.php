<?php

class CElement_Component_Repeater extends CElement_Component {
    protected $itemBuilder;

    protected $canDelete;

    protected $canAdd;

    protected $addLabel;

    protected $deleteLabel;

    protected $minItem;

    public function __construct() {
        parent::__construct();
        $this->canDelete = true;
        $this->canAdd = true;
        $this->deleteLabel = 'Delete';
        $this->addLabel = 'New Item';
        $this->minItem = 1;
    }

    public static function factory($id = null) {
        return new static($id);
    }

    public function setItemBuilder($itemBuilder) {
        $this->itemBuilder = $itemBuilder;

        return $this;
    }

    public function setMinItem($minItem) {
        $this->minItem = (int) $minItem;

        return $this;
    }

    protected function build() {
        $config = [
            'minItem' => $this->minItem,
        ];
        $this->addClass('cres:element:component:Repeater');
        $this->setAttr('cres-element', 'component:Repeater');
        $this->setAttr('cres-config', htmlspecialchars(json_encode($config), ENT_QUOTES, 'UTF-8'));
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
