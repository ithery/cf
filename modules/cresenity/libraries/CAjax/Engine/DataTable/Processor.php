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

    public function __construct(CAjax_Engine $engine) {
        $this->engine = $engine;
        $this->input = $engine->getInput();
        $this->data = $engine->getData();
        $this->method = $engine->getMethod();
    }

}
