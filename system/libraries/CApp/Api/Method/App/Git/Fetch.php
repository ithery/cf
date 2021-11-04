<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

/**
 * @author Muhammad Harisuddin Thohir <me@harisuddin.com>
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2020, 11:13:37 AM
 */
class CApp_Api_Method_App_Git_Fetch extends CApp_Api_Method_App {
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
                $gitPath = escapeshellcmd($gitPath);
                $gitBaseCommand = 'git';
                if (strlen($gitDir) > 0) {
                    $gitBaseCommand = 'git --git-dir "' . escapeshellcmd($gitDir) . '"';
                }

                $pwd = shell_exec("cd ${gitPath} && pwd");
                $execute = "cd \"${gitPath}\" && ${gitBaseCommand} fetch";

                $output .= "working on directory ${pwd}";
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
