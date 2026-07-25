<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_App_GitStatus extends CApp_Api_Method_App {
    /**
     * Runs a `git status` on the app's directory via shell_exec and returns the output.
     *
     * @return $this
     */
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $output = '';
        $data = [];

        try {
            $output = shell_exec("cd application/{$this->appCode} && git status");
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
