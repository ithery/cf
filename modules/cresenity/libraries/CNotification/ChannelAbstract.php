<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CNotification_ChannelAbstract implements CNotification_ChannelInterface {

    protected static $channelName;
    protected static $config;

    public function __construct($config = []) {
        $this->config = $config;
    }

    public function queue($className, array $options) {
        $options = [
            'channel' => static::$channelName,
            'className' => $className,
            'options' => $options,
        ];

        $taskQueue = CNotification_TaskQueue_NotificationSender::dispatch($options);
    }

    public function send($className, array $options = []) {

        $message = new $className();
        $message->setOptions($options);
        $result = $message->execute();

        $messageResult = $this->handleResult($message, $result);
        return $messageResult;
    }

    public function handleResult($message, $result) {
        if (is_array($result)) {
            $result = c::collect($result);
        }
        $result->each(function($value, $key) use ($message) {
            $errCode = 0;
            $errMessage = '';
            $logNotificationModel = null;

            $logNotificationModel = $this->insertLogNotification($message, $value);


            if ($errCode == 0) {
                try {
                    $result = $this->handleMessage($value, $logNotificationModel);
                } catch (Exception $ex) {
                    $errCode++;
                    $errMessage = $ex->getMessage();
                }
            }
            if ($errCode > 0) {
                $logNotificationModel->error = $ex->getMessage();
                $logNotificationModel->notification_status = 'FAILED';
            } else {

                $logNotificationModel->notification_status = 'SUCCESS';
            }


            $logNotificationModel->save();
        });
    }

    protected function insertLogNotification($message, $result) {
        $model = CNotification::manager()->createLogNotificationModel();

        $options = c::collect($result);
        $model->recipient = $options->pull('recipient');
        $model->subject = $options->pull('subject');
        $model->message = $options->pull('message');
        $model->options = json_encode($options->all());
        $model->createdby = CApp_Base::username();
        $model->updatedby = CApp_Base::username();
        $model->created = CApp_Base::now();
        $model->updated = CApp_Base::now();

        $model->save();
        return $model;
    }

}
