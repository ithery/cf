<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

/**
 * @author Muhammad Harisuddin Thohir <me@harisuddin.com>
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2020, 11:13:37 AM
 */
class CApp_Api_Method_App_Git extends CApp_Api_Method_App {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $output = '';
        $successOutput = '';
        $errorOutput = '';
        $data = [];
        $command = carr::get($this->request(), 'command');
        $isFramework = carr::get($this->request(), 'isFramework', '0');
        $allowedCommand = ['status', 'fetch', 'pull'];
        $avalableAppList = CF::getAvailableAppCode();

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
            $errMessage = 'appCode ' . $this->appCode . ' not found on :' . json_encode($avalableAppList);
        }

        if ($errCode == 0) {
            try {
                $pwd = '';
                $execute = '';

                if ($isFramework == '0') {
                    if (CServer::getOS() == CServer::OS_WINNT) {
                        $pwd = shell_exec("cd application/{$this->appCode} && echo %cd%");
                        $execute = "cd application/{$this->appCode} && git {$command}";
                    } else {
                        $pwd = shell_exec("cd application/{$this->appCode} && pwd");
                        $execute = "cd application/{$this->appCode} && git {$command}";
                    }
                } else {
                    if (CServer::getOS() == CServer::OS_WINNT) {
                        $pwd = shell_exec('echo %cd%');
                        $execute = "git {$command}";
                    } else {
                        $pwd = shell_exec('pwd');
                        $execute = "git {$command}";
                    }
                }

                $output .= 'working on directory ' . trim($pwd) . ' user:' . get_current_user() . PHP_EOL;

                $process = new Process($execute);
                $process->run();

                $output .= $process->getOutput();
                $successOutput = $output;
                $output .= $errorOutput = $process->getErrorOutput();

                if ($command === 'pull') {
                    CView::blade()->clearCompiled();
                }
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            $data = [
                'output' => $output,
                'successOutput' => $successOutput,
                'errorOutput' => $errorOutput,
            ];
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
