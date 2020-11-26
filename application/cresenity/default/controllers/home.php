<?php

/**
 * Description of home
 *
 * @author Hery
 */
use League\Csv\Writer;

Class Controller_Home extends CController {

    public function index() {

        //we create the CSV into memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        //we insert the CSV header
        $csv->insertOne(['firstname', 'lastname', 'email']);

        // Because you are providing the filename you don't have to
        // set the HTTP headers Writer::output can
        // directly set them for you
        // The file is downloadable
        $csv->output('users.csv');
    }

}
