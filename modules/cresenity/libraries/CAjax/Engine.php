<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 10:58:20 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CAjax_Engine implements CAjax_EngineInterface {

    /**
     *
     * @var CAjax_Method
     */
    protected $ajaxMethod;
    protected $input;

    /**
     * 
     * @param string $methodCall
     */
    public function __construct(CAjax_Method $ajaxMethod) {
        $this->ajaxMethod = $ajaxMethod;
        $this->input = array_merge($_GET, $_POST);
    }

    public function setInput(array $input) {
        $this->input = $input;
    }

}
