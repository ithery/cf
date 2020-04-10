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

    /**
     *
     * @var CElement_Component_DataTable
     */
    protected $table;

    public function __construct(CAjax_Engine $engine) {
        $this->engine = $engine;
        $this->input = $engine->getInput();
        $this->data = $engine->getData();
        $this->method = $engine->getMethod();
        $this->parameter = new CAjax_Engine_DataTable_Parameter($this);
    }

    /**
     * 
     * @return array
     */
    public function columns() {
        return $this->table()->getColumns();
    }

    /**
     * 
     * @return string
     */
    public function domain() {
        return $this->table()->getDomain();
    }

    /**
     * 
     * @return string
     */
    public function actionLocation() {
        return $this->table->getActionLocation();
    }

    public function table() {
        if ($this->table == null) {
            $data = $this->engine->getData();
            $this->table = unserialize(carr::get($data, 'table'));
        }

        return $this->table;
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

    public function sortingCol() {
        return carr::get($this->parameter->sortingData(), '0.0');
    }

    public function sortingDir() {
        return carr::get($this->parameter->sortingData(), '0.1');
    }

    public function getData($key, $default = null) {
        return carr::get($this->data, $key, $default);
    }

}
