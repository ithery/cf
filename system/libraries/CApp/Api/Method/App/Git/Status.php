<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

class CApp_Api_Method_App_Git_Status extends CApp_Api_Method_App {
    /**
     * Runs a `git status` on the app's git directory and returns the output.
     *
     * @return $this
     */
    public function execute() {
        $output = '';
        $successOutput = '';
        $errorOutput = '';
        $gitPath = carr::get($this->request(), 'gitPath');
        $gitDir = carr::get($this->request(), 'gitDir');

        if ($this->errCode == 0) {
            try {
                if (strlen($gitPath) == 0) {
                    $gitPath = "application/{$this->appCode}";
                }
                //$gitPath = escapeshellcmd($gitPath);
                $gitBaseCommand = 'git';
                if (strlen($gitDir) > 0) {
                    $gitBaseCommand = 'git --git-dir ' . $gitDir . '';
                    $output .= 'gitDir:' . realpath($gitDir) . PHP_EOL;
                }

                $pwd = shell_exec("cd ${gitPath} && pwd");
                $execute = "cd ${gitPath} && ${gitBaseCommand} status";

                $output .= "working on directory ${pwd}";
                $process = new Process($execute);
                $output .= $execute . PHP_EOL;
                $process->run();

                $output .= $process->getOutput();
                $successOutput = $output;
                $output .= $errorOutput = $process->getErrorOutput();
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
