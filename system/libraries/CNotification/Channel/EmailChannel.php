<?php

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
        $errCode = 0;
        $errMessage = '';
        if ($errCode == 0) {
            try {
                $options['cc'] = $cc;
                $options['bcc'] = $bcc;
                $options['attachments'] = $attachment;

                $response = CEmail::sender($options)->send($to, $subject, $message, $options);
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }
        if ($errCode > 0) {
            throw new CNotification_Exception($errMessage);
        }
        return $response;
    }

    protected function sendEmail() {
    }
}
