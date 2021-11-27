<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 4, 2019, 3:47:02 PM
 */
trait CElement_Component_DataTable_Trait_GridViewTrait {
    protected $haveDataTableViewAction;

    protected $dataTableViewColCount;

    protected $dataTableView;

    public function setHaveDataTableViewAction($bool = true) {
        $this->haveDataTableViewAction = $bool;

        return $this;
    }

    public function setDataTableViewCol() {
        $this->dataTableView = CConstant::TABLE_VIEW_COL;

        return $this;
    }

    public function setDataTableViewRow() {
        $this->dataTableView = CConstant::TABLE_VIEW_ROW;

        return $this;
    }
}
