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
                // status
                $pwd = shell_exec("cd application/{$this->appCode} && pwd");
                $execute = "cd application/{$this->appCode} && git status";
                $process = new Process($execute);
                $process->run();

                $outputStatus = $process->getOutput();
                $outputStatusArray = explode("\n", $outputStatus);

                $isAfterUntracked = false;
                $untrackedFiles = [];
                foreach ($outputStatusArray as $line) {
                    if ($isAfterUntracked) {
                        $lineParsed = $line;
                        //we need to remove #
                        if (substr($lineParsed, 0, 1) == '#') {
                            $lineParsed = substr($lineParsed, 1);
                            $lineParsed = trim($lineParsed);
                            if (strlen($lineParsed) > 0 && substr($lineParsed, 0, 1) != '(') {
                                $untrackedFiles[] = $lineParsed;
                            }
                        } else {
                            break;
                        }
                    }
                    if ($isAfterUntracked == false) {
                        if (strpos($line, 'Untracked files:') !== false) {
                            $isAfterUntracked = true;
                        }
                    }
                }


                // clean -n
                $pwd = shell_exec("cd application/{$this->appCode} && pwd");
                $execute = "cd application/{$this->appCode} && git clean -n";
                $process = new Process($execute);
                $process->run();

                $outputInfo = $process->getOutput();
                $output .= $process->getOutput();
                $outputInfoArray = explode("\n", $outputInfo);

                $wouldRemoveFiles = [];
                foreach ($outputInfoArray as $line) {
                    $lineParsed = $line;
                    //we need to remove #
                    if (substr($lineParsed, 0, 13) == 'Would remove ') {
                        $lineParsed = substr($lineParsed, 13);
                        $lineParsed = trim($lineParsed);
                        if (substr($lineParsed, 0, 1) != '(') {
                            $wouldRemoveFiles[] = $lineParsed;
                        }
                    } elseif (substr($lineParsed, 0, 11) == 'Would skip ') {
                        //do nothing
                    } else {
                        break;
                    }
                }

                // checking for diff
                foreach ($wouldRemoveFiles as $file) {
                    if (!in_array($file, $untrackedFiles)) {
                        $this->errCode++;
                        $errorOutput .= 'would remove file ' . $file . " is not exists in untracked file \n";
                    }
                }
                foreach ($untrackedFiles as $file) {
                    if (!in_array($file, $wouldRemoveFiles)) {
                        $this->errCode++;
                        $errorOutput .= 'untracked file ' . $file . " is not exists in would remove file \n";
                    }
                }
                $output .= $this->errCode;
                if (!$this->errCode) {
                    $pwd = shell_exec("cd application/{$this->appCode} && pwd");
                    $execute = "cd application/{$this->appCode} && git clean -f";

                    $output .= "working on directory $pwd";
                    $process = new Process($execute);
                    $process->run();

                    $output .= $process->getOutput();
                    $successOutput = $output;
                    $output .= $errorOutput = $process->getErrorOutput();
                }
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
