<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Muhammad Harisuddin Thohir <me@harisuddin.com>
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 9, 2020, 2:37:17 PM
 */
class CApp_Api_Method_App_GitStatus extends CApp_Api_Method_App {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $output = '';
        $data = [];

        try {
            $output = shell_exec("cd application/$this->appCode && git status");
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        if ($errCode == 0) {
            $data = [
                'output' => $output,
            ];
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
