<?php

defined('SYSPATH') or die('No direct access allowed.');

use Symfony\Component\Process\Process;

/**
 * @author Muhammad Harisuddin Thohir <me@harisuddin.com>
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2020, 11:13:37 AM
 */
class CApp_Api_Method_App_Info extends CApp_Api_Method_App {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $output = '';
        $successOutput = '';
        $errorOutput = '';
        $data = [];
        $data['CFVersion'] = CF::version();
        $data['ability'] = [
            'appConfigGet' => [
                'class' => 'CApp_Api_Method_App_Config_Get',
                'exist' => false,
            ],
            'appConfigEdit' => [
                'class' => 'CApp_Api_Method_App_Config_Edit',
                'exist' => false,
            ],
            'appGitStatus' => [
                'class' => 'CApp_Api_Method_App_Git_Status',
                'exist' => false,
            ],
            'appGitFetch' => [
                'class' => 'CApp_Api_Method_App_Git_Fetch',
                'exist' => false,
            ],
            'appGitPull' => [
                'class' => 'CApp_Api_Method_App_Git_Pull',
                'exist' => false,
            ],
            'appGitClean' => [
                'class' => 'CApp_Api_Method_App_Git_Clean',
                'exist' => false,
            ],
            'appGitCheckout' => [
                'class' => 'CApp_Api_Method_App_Git_Checkout',
                'exist' => false,
            ],
            'serverGitStatus' => [
                'class' => 'CApp_Api_Method_Server_Git_Status',
                'exist' => false,
            ],
            'serverGitFetch' => [
                'class' => 'CApp_Api_Method_Server_Git_Fetch',
                'exist' => false,
            ],
            'serverGitPull' => [
                'class' => 'CApp_Api_Method_Server_Git_Pull',
                'exist' => false,
            ],
            'serverGitClean' => [
                'class' => 'CApp_Api_Method_Server_Git_Clean',
                'exist' => false,
            ],
            'serverGitCheckout' => [
                'class' => 'CApp_Api_Method_Server_Git_Checkout',
                'exist' => false,
            ],
        ];
        foreach ($data['ability'] as $key => $val) {
            $exist = class_exists($val['class']);
            $data['ability'][$key]['exist'] = $exist;
        }
        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
