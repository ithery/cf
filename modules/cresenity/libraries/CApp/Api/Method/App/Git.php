<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Muhammad Harisuddin Thohir <me@harisuddin.com>
 * @since Mar 10, 2020, 11:13:37 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_App_Git extends CApp_Api_Method_App {

    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $output = '';
        $data = array();
        $command = carr::get($this->request(), 'command');
        $allowedCommand = ['status', 'fetch', 'pull'];
        $avalableAppList = explode('
', shell_exec("cd application && ls"));
        
        if (!in_array($command, $allowedCommand)) {
            $errCode++;
            $errMessage = 'Command is not allowed';
        }

        if (empty($command)) {
            $errCode++;
            $errMessage = 'Command is required';
        }

        if (!in_array($this->appCode, $avalableAppList)) {
            $errCode++;
            $errMessage = "appCode '$this->appCode' not found";
        }

        if ($errCode == 0) {
            try {
                $pwd = shell_exec("cd application/$this->appCode && pwd");
                $output .= "working on directory $pwd";
                $output .= shell_exec("cd application/$this->appCode && git $command");
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
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
