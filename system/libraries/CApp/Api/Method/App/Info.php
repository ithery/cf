<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

/**
 * @author Muhammad Harisuddin Thohir <me@harisuddin.com>
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2020, 11:13:37 AM
 */
class CApp_Api_Method_App_Info extends CApp_Api_Method_App {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $output = '';
        $successOutput = '';
        $errorOutput = '';
        $data = [];

        $data['CFVersion'] = CF::version();
        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
