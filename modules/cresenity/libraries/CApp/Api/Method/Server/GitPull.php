<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 14, 2018, 4:40:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_Server_GitPull extends CApp_Api_Method_Server {

    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;

        $path = DOCROOT . '../bin/gitFile/';
        $filename = '';

        $request = $this->request();
        $appCode = carr::get($request, 'app_code');



        $data = array();

        try {
            file_put_contents($path . $appCode, "");
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}
