<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 29, 2019, 10:45:30 AM
 */
class CAjax_Engine_SelectSearch_Parameter {
    protected $requestGet;

    protected $processor;

    public function __construct(CAjax_Engine_SelectSearch_Processor $processor) {
        $this->requestGet = $_GET;
        $this->processor = $processor;
    }

    public function pageSize() {
        return carr::get($this->requestGet, 'limit');
    }

    public function page() {
        return carr::get($this->requestGet, 'page', 1);
    }

    public function searchTerm() {
        return carr::get($this->requestGet, 'q', carr::get($this->requestGet, 'term', ''));
    }

    public function callback() {
        return carr::get($this->requestGet, 'callback');
    }
}
