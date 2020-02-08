<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    'log_notification_model' => CNotification_Model_LogNotification::class,
    'queue' => array(
    ),
    'email' => array(
        'vendor' => 'sendgrid',
    ),
    'sms' => array(
        'vendor' => 'nexmo',
    ),
);
