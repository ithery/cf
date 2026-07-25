<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

class CApp_Api_Method_App_Git_Checkout extends CApp_Api_Method_App {
    /**
     * Runs `git checkout` for the requested branch on the app's git
     * directory and returns the output.
     *
     * @return $this
     */
    public function execute() {
        $output = '';
        $successOutput = '';
        $errorOutput = '';
        $branch = c::get($this->request(), 'gitBranch');
        if (!$branch) {
            $this->errCode++;
            $this->errMessage = 'Branch is required';
        }
        if ($this->errCode == 0) {
            try {
                $pwd = '';
                $execute = '';

                $pwd = shell_exec("cd application/{$this->appCode} && pwd");
                $execute = "cd application/{$this->appCode} && git checkout {$branch}";

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
