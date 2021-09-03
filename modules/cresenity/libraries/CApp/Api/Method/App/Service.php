<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

/**
 * @author Muhammad Harisuddin Thohir <haris@thohir.com>
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 31, 2021, 06:57:37 PM
 */
class CApp_Api_Method_App_Service extends CApp_Api_Method_App {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $data = [];

        $status = 'not found';
        $time = '';
        $since = '';

        $service = carr::get($this->request(), 'service');
        $command = carr::get($this->request(), 'command');

        $allowedCommand = ['status', 'start', 'stop', 'restart'];

        if (!in_array($command, $allowedCommand)) {
            $errCode++;
            $errMessage = 'Command is not allowed';
        }

        if (empty($command)) {
            $errCode++;
            $errMessage = 'Command is required';
        }

        if (empty($service)) {
            $errCode++;
            $errMessage = 'Service is required';
        }

        if ($errCode == 0) {
            try {
                $execute = "systemctl $command $service | grep \"Active:\"";
                $output = '';
                $errorOutput  = '';

                $process = new Process($execute);
                $process->run();

                $output .= $process->getOutput();
                $errorOutput .= $process->getErrorOutput();
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }

        if (!empty($output)) {
            preg_match('/Active: (.+?) since (.+?);(.*)/', $output, $matches, PREG_OFFSET_CAPTURE);
            if ($matches) {
                $status = carr::get($matches, '1.0', $status);
                $statuses = explode(' ', $status);
                $status = carr::get($statuses, '0', $status);
                $time = carr::get($matches, '2.0');
                $since = carr::get($matches, '3.0');
            } else {
                preg_match('/Active: (.+?) /', $output, $matches, PREG_OFFSET_CAPTURE);
                $status = carr::get($matches, '1.0', $status);
            }

            $status = str_replace('(', '', $status);
            $status = str_replace(')', '', $status);
            $status = str_replace('exited', 'stopped', $status);
            $status = str_replace('inactive', 'stopped', $status);
            $status = str_replace('deactivating', 'stopped', $status);
            $status = str_replace('active', 'running', $status);
        }

        if ($errCode == 0) {
            $data = [
                'status' => $status,
                'time' => $time,
                'since' => $since,
                'output' => $output,
                'errorOutput' => $errorOutput,
            ];
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
