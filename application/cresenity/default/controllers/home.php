<?php

/**
 * Description of home
 *
 * @author Hery
 */
use League\Csv\Writer;

Class Controller_Home extends CController {

    public function index() {

        return CF::response()->view('welcome');
    }

}
