<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CApp_Api_MethodInterface {
    /**
     * Executes the API method and returns the method instance holding the result.
     *
     * @return $this
     */
    public function execute();
}
