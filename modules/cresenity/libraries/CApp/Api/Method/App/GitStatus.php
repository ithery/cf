<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Muhammad Harisuddin Thohir <me@harisuddin.com>
 * @since Mar 9, 2020, 2:37:17 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_App_GitStatus extends CApp_Api_Method_App {

    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $output = '';
        $data = array();
        $appCode = carr::get($this->request(), 'appCode');
        
        try{
            $output = shell_exec("cd application/$appCode && git status");
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        $data = [
            'output' => $output,
        ];

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}
