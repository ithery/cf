<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CAjax_Engine_SelectSearch_Processor implements CAjax_Engine_SelectSearch_ProcessorInterface {
    use CElement_FormInput_SelectSearch_Trait_SelectSearchUtilsTrait;

    /**
     * @var CAjax_Engine
     */
    protected $engine;

    /**
     * @var array
     */
    protected $input;

    /**
     * @param array data
     */
    protected $data;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var CAjax_Engine_SelectSearch_Parameter
     */
    protected $parameter;

    public function __construct(CAjax_Engine $engine) {
        $this->engine = $engine;
        $this->input = $engine->getInput();
        $this->data = $engine->getData();
        $this->method = $engine->getMethod();
        $this->parameter = new CAjax_Engine_SelectSearch_Parameter($this);
    }

    /**
     * @return null|string
     */
    public function formatResult() {
        return unserialize(carr::get($this->data, 'formatResult'));
    }

    /**
     * @return null|string
     */
    public function formatSelection() {
        return unserialize(carr::get($this->data, 'formatSelection'));
    }

    /**
     * @return string
     */
    public function keyField() {
        return carr::get($this->data, 'keyField', carr::get($this->data, 'key_field'));
    }

    /**
     * @return array
     */
    public function searchField() {
        return carr::wrap(carr::get($this->data, 'searchField', carr::get($this->data, 'search_field')));
    }

    /**
     * @return array
     */
    public function searchFullTextField() {
        return carr::wrap(carr::get($this->data, 'searchFullTextField'));
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

    public function callback() {
        return $this->parameter->callback();
    }

    /**
     * Returns the ID of the current search.
     *
     * @return int
     */
    public function searchIds() {
        return carr::wrap($this->parameter->id());
    }

    public function input() {
        return $this->engine->getInput();
    }

    public function query() {
        return carr::get($this->data, 'query');
    }

    /**
     * @return CManager_DataProviderAbstract
     */
    public function dataProvider() {
        return unserialize(carr::get($this->data, 'dataProvider'));
    }

    public function dependsOn() {
        return unserialize(carr::get($this->data, 'dependsOn'));
    }

    public function prependData() {
        return unserialize(carr::get($this->data, 'prependData'));
    }
}
