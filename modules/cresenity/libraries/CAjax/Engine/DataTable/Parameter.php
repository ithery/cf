<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 29, 2019, 10:45:30 AM
 * @license Ittron Global Teknologi <ittron.co.id>
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

}
