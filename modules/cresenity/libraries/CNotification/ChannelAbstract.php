<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CNotification_ChannelAbstract implements CNotification_ChannelInterface {

    protected static $channelName;
    protected $config;

    public function __construct($config = []) {
        $this->config = $config;
    }

    public function send($className, array $options = []) {
        $notificationSenderJobClass = CF::config('notification.task_queue.notification_sender', CNotification_TaskQueue_NotificationSender::class);

        $options = [
            'channel' => static::$channelName,
            'className' => $className,
            'options' => $options,
        ];

        $isQueued = CF::config('notification.queue.queued');
        if ($isQueued) {
            $taskQueue = $notificationSenderJobClass::dispatch($options);
            if ($customConnection = CF::config('notification.queue.connection')) {
                $taskQueue->onConnection($customQueue);
            }
            if ($customQueue = CF::config('notification.queue.name')) {
                $taskQueue->onQueue($customQueue);
            }
        } else {
            $notificationSenderJobClass::dispatchNow($options);
        }
    }

    public function sendWithoutQueue($className, array $options = []) {

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
        $hasError = false;
        $result->each(function($value, $key) use ($message, &$hasError) {
            $errCode = 0;
            $errMessage = '';
            $logNotificationModel = null;

            $logNotificationModel = $this->insertLogNotification($message, $value);

            if ($errCode == 0) {
                try {
                    $result = $this->handleMessage($value, $logNotificationModel);
                    $vendorResponse = $result;
                    if (is_array($vendorResponse) || is_object($vendorResponse)) {
                        $vendorResponse = json_encode($vendorResponse);
                    }

                    $logNotificationModel->vendor_response = $vendorResponse;
                    
                    CDaemon::log('vendor response:' . $vendorResponse);
                } catch (Exception $ex) {
                    //throw $ex;
                    $errCode++;
                    $errMessage = $ex->getMessage() . ':' . $ex->getTraceAsString();
                }
            }
            if ($errCode > 0) {
                $logNotificationModel->error = $errMessage;
                $logNotificationModel->notification_status = 'FAILED';
            } else {

                $logNotificationModel->notification_status = 'SUCCESS';
            }


            $logNotificationModel->save();
            //$message->onNotificationSent($logNotificationModel);
        });
    }

    protected function insertLogNotification($message, $result) {
        $model = CNotification::manager()->createLogNotificationModel();
        $options = c::collect($result);
        $recipient = $options->pull('recipient');
        if (is_array($recipient)) {
            $recipient = CHelper::json()->encode($recipient);
        }

        $orgId = $options->pull('orgId');
        if (strlen($orgId) == 0) {
            $orgId = CApp_Base::orgId();
        }

        $vendor = $this->getVendorName();

        $model->message_class = get_class($message);
        $model->vendor = $vendor;
        $model->org_id = CApp_Base::orgId();
        $model->channel = static::$channelName;
        $model->notification_status = 'PENDING';
        $model->is_read = 0;
        $model->recipient = $recipient;
        $model->subject = $options->pull('subject');
        $model->message = $options->pull('message');
        $model->ref_type = $options->pull('refType');
        $model->ref_id = $options->pull('refId');
        $model->options = json_encode($options->all());
        $model->createdby = CApp_Base::username();
        $model->updatedby = CApp_Base::username();
        $model->created = CApp_Base::now();
        $model->updated = CApp_Base::now();
        $model->status = 1;
        $model->save();
        return $model;
    }

    public function getVendorName() {
        $vendor = carr::get($this->config, 'vendor');
        if (strlen($vendor) == 0) {
            $vendor = CF::config('notification.' . strtolower(cstr::snake(static::$channelName)) . '.vendor');
        }
        return $vendor;
    }

    /**
     * 
     * @return CNotification_MessageAbstract
     */
    public function createMessage($data) {
        $vendorConfig = carr::get($this->config, 'vendor_config');
        if (!is_array($vendorConfig)) {
            $vendorConfig = CF::config('vendor.' . $this->getVendorName());
        }
       
        
        if (!is_array($vendorConfig)) {
            $vendorConfig = [];
        }
        return CNotification::manager()->createMessage($this->getVendorName(), $vendorConfig, $data);
    }

    protected function dispatchQueuedConversions(CApp_Model_Interface_ResourceInterface $resource, CResources_ConversionCollection $queuedConversions) {
        
    }

}
