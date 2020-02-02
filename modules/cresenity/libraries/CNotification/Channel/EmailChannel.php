<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CNotification_Channel_EmailChannel extends CNotification_ChannelAbstract {

    protected static $channelName = 'Email';

    protected function handleMessage($data, $logNotificationModel) {
        $to = carr::get($data, 'recipient');
        $subject = carr::get($data, 'subject');
        $message = carr::get($data, 'message');
        $attachment = carr::get($data, 'attachment', []);
        $cc = carr::get($data, 'cc', []);
        $bcc = carr::get($data, 'bcc', []);
        $options = carr::get($data, 'options', []);
        $errCode=0;
        $errMessage='';
        if ($errCode == 0) {
            try {
                
                cmail::send_smtp($to, $subject, $message, $attachment, $cc, $bcc, $options);
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }
        if($errCode>0) {
            throw new CNotification_Exception($errMessage);
        }
        return true;
    }

    protected function sendEmail() {
        if ($err_code == 0) {
            try {
                cmail::send_smtp($this->to, $this->subject, $this->message, $this->attachment, $cc, $bcc, $options);
            } catch (Exception $ex) {
                $err_code++;
                $err_message = $ex->getMessage();
            }
        }
    }

}
