<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 3:01:36 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CAjax_Engine_DataTable_Processor implements CAjax_Engine_DataTable_ProcessorInterface {

    /**
     *
     * @var CAjax_Engine
     */
    protected $engine;

    /**
     *
     * @var array
     */
    protected $input;

    /**
     * 
     * @param array data
     */
    protected $data;

    /**
     *
     * @var string
     */
    protected $method;

    /**
     *
     * @var CAjax_Engine_DataTable_Parameter
     */
    protected $parameter;

    public function __construct(CAjax_Engine $engine) {
        $this->engine = $engine;
        $this->input = $engine->getInput();
        $this->data = $engine->getData();
        $this->method = $engine->getMethod();
        $this->parameter = new CAjax_Engine_DataTable_Parameter($this);
    }

    public function columns() {
        $data = $this->engine->getData();
        return carr::get($data, 'columns');
    }

    public function table() {
        $data = $this->engine->getData();
        return carr::get($data, 'table');
    }

    public function pageSize() {
        return $this->parameter->pageSize();
    }

    public function page() {
        return $this->parameter->page();
    }

    public function searchTerm() {
        return $this->parameter->searchTerm();
    }

}
