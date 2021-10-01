<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

class CApp_Api_Method_Server_Git_Clean extends CApp_Api_Method_Server {
    public function execute() {
        $output = '';
        $successOutput = '';
        $errorOutput = '';

        if ($this->errCode == 0) {
            try {
                $pwd = '';
                $execute = '';

                $pwd = shell_exec("pwd");
                $execute = "git checkout -- .";

                $output .= "working on directory $pwd";
                $process = new Process($execute);
                $process->run();

                $output .= $process->getOutput();
                $successOutput = $output;
                $output .= $errorOutput = $process->getErrorOutput();

                CView::blade()->clearCompiled();
            } catch (Exception $ex) {
                $this->errCode++;
                $this->errMessage = $ex->getMessage();
            }
        }

        $this->data = [
            'output' => $output,
            'successOutput' => $successOutput,
            'errorOutput' => $errorOutput,
        ];

        return $this;
    }
}
