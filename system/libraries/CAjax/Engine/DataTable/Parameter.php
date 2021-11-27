<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 29, 2019, 10:45:30 AM
 */
class CAjax_Engine_DataTable_Parameter {
    protected $requestGet;

    protected $processor;

    public function __construct(CAjax_Engine_DataTable_Processor $processor) {
        $this->requestGet = $_GET;
        $this->processor = $processor;
    }

    public function pageSize() {
        return carr::get($this->requestGet, 'iDisplayLength');
    }

    public function page() {
        $pageSize = $this->pageSize();
        if (strlen($pageSize) > 0 && $pageSize != 0) {
            return (carr::get($this->requestGet, 'iDisplayStart') / $pageSize) + 1;
        }

        return 1;
    }

    public function searchTerm() {
        return carr::get($this->requestGet, 'sSearch', '');
    }

    public function sortingData() {
        $table = $this->processor->table();
        $columns = $this->processor->columns();
        $iSortingCols = intval(carr::get($this->requestGet, 'iSortingCols', 0));
        $sortingData = [];
        if (isset($this->requestGet['iSortCol_0'])) {
            for ($i = 0; $i < $iSortingCols; $i++) {
                $i2 = 0;
                if ($table->checkbox) {
                    $i2 = -1;
                }
                $iSortColIndex = carr::get($this->requestGet, 'iSortCol_' . $i);
                if ($iSortColIndex != null) {
                    $iSortColIndex = $iSortColIndex + $i2;
                    $iSortDir = carr::get($this->requestGet, 'sSortDir_' . ($iSortColIndex), 'asc');
                    $fieldName = carr::get(carr::get($columns, $iSortColIndex), 'fieldname');
                    $sortableEnabled = carr::get($this->requestGet, 'bSortable_' . $iSortColIndex) == 'true';
                    if ($sortableEnabled) {
                        $sortingData[] = [$fieldName, $iSortDir];
                    }
                }
            }
        }

        return $sortingData;
    }
}
