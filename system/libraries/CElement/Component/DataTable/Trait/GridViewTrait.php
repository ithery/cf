<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CElement_Component_DataTable_Trait_GridViewTrait {
    /**
     * @var bool
     */
    protected $haveDataTableViewAction;

    /**
     * @var int
     */
    protected $dataTableViewColCount;

    /**
     * @var string
     */
    protected $dataTableView;

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setHaveDataTableViewAction($bool = true) {
        $this->haveDataTableViewAction = $bool;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDataTableViewCol() {
        $this->dataTableView = CConstant::TABLE_VIEW_COL;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDataTableViewRow() {
        $this->dataTableView = CConstant::TABLE_VIEW_ROW;

        return $this;
    }
}
