<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

/**
 * @author Muhammad Harisuddin Thohir <me@harisuddin.com>
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2020, 11:13:37 AM
 */
class CApp_Api_Method_App_Git_Clean extends CApp_Api_Method_App {
    public function execute() {
        $output = '';
        $successOutput = '';
        $errorOutput = '';

        if ($this->errCode == 0) {
            try {
                $pwd = '';
                $execute = '';

                $pwd = shell_exec("cd application/{$this->appCode} && pwd");
                $execute = "cd application/{$this->appCode} && git checkout -- .";

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
