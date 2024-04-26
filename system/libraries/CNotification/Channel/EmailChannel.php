<?php

class CNotification_Channel_EmailChannel extends CNotification_ChannelAbstract {
    public function __construct($config = []) {
        parent::__construct($config);
        $this->channelName = 'Email';
    }

    protected function handleMessage($data, $logNotificationModel) {
        $to = carr::get($data, 'recipient');
        $subject = carr::get($data, 'subject');
        $message = carr::get($data, 'message');
        $attachment = carr::get($data, 'attachments', carr::get($data, 'attachment', []));
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
                $senderOptions = $options;
                // $emailConfig = CF::config('notification.email');
                // $vendor = carr::get($emailConfig, 'vendor');
                // if ($vendor) {
                //     $senderOptions['driver'] = $vendor;
                //     $password = CF::config('vendor.' . $vendor . '.key');
                //     if ($password) {
                //         $senderOptions['password'] = $password;
                //     }
                // }
                $sender = CEmail::sender($senderOptions);

                $response = $sender->send($to, $subject, $message, $options);
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
