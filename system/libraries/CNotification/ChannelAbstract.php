<?php

abstract class CNotification_ChannelAbstract implements CNotification_ChannelInterface {
    protected $channelName;

    protected $config;

    /**
     * @var \Opis\Closure\SerializableClosure|string
     */
    protected $messageHandler = null;

    public function __construct($config = []) {
        $this->config = $config;
        $this->channelName = 'Custom';
    }

    /**
     * @param Closure $messageHandler
     *
     * @return $this
     */
    public function setMessageHandler($messageHandler) {
        if ($messageHandler instanceof Closure) {
            $messageHandler = new \Opis\Closure\SerializableClosure($messageHandler);
        }
        $this->messageHandler = $messageHandler;

        return $this;
    }

    public function getChannelConfig($key) {
        return CF::config(
            'notification.' . strtolower(cstr::snake($this->channelName)) . '.' . $key,
            CF::config('notification.' . $key)
        );
    }

    /**
     * @param string $className
     * @param array  $options
     *
     * @return CQueue_AbstractTask
     */
    public function send($className, array $options = [], array $config = []) {
        $notificationSenderJobClass = CF::config('notification.task_queue.notification_sender', CNotification_TaskQueue_NotificationSender::class);

        $isQueued = carr::get($config, 'queue.queued', $this->getChannelConfig('queue.queued'));

        $options = [
            'channel' => $this->channelName,
            'className' => $className,
            'options' => $options,
        ];

        if ($isQueued) {
            $queueConnection = carr::get($config, 'queue.connection', $this->getChannelConfig('queue.connection'));
            $queueName = carr::get($config, 'queue.name', $this->getChannelConfig('queue.name'));
            $taskQueue = call_user_func([$notificationSenderJobClass, 'dispatch'], $options);
            if ($queueConnection) {
                $taskQueue->onConnection($queueConnection);
            }
            if ($queueName) {
                $taskQueue->onQueue($queueName);
            }
        } else {
            $taskQueue = call_user_func([$notificationSenderJobClass, 'dispatchNow'], $options);
        }

        return $taskQueue;
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
        $result->each(function ($value, $key) use ($message, &$hasError) {
            $errCode = 0;
            $errMessage = '';
            $logNotificationModel = null;

            $logNotificationModel = $this->insertLogNotification($message, $value);

            if ($errCode == 0) {
                try {
                    if ($this->messageHandler != null) {
                        $result = $this->messageHandler->__invoke($value, $logNotificationModel);
                    } else {
                        $result = $this->handleMessage($value, $logNotificationModel);
                    }

                    $vendorResponse = $result;
                    if ($vendorResponse instanceof CVendor_SendGrid_Response) {
                        $vendorResponse = [
                            'statusCode' => $vendorResponse->statusCode(),
                            'body' => $vendorResponse->body(),
                            'headers' => $vendorResponse->headers()
                        ];
                    }

                    if ($vendorResponse instanceof CVendor_Firebase_Messaging_MulticastSendReport) {
                        $resultResponse = [];
                        $resultResponse['success'] = [];
                        $resultResponse['fail'] = [];
                        foreach ($vendorResponse->successes()->getItems() as $report) {
                            $resultResponse['success'][] = [
                                'type' => $report->target()->type(),
                                'value' => $report->target()->value(),
                                'result' => $report->result(),
                            ];
                        }
                        foreach ($vendorResponse->failures()->getItems() as $report) {
                            $resultResponse['fail'][] = [
                                'type' => $report->target()->type(),
                                'value' => $report->target()->value(),
                                'result' => $report->result(),
                                'error' => $report->error() instanceof Exception ? $report->error()->getMessage() : $report->error(),
                            ];
                        }
                        $vendorResponse = $resultResponse;
                    }

                    if (!is_string($vendorResponse)) {
                        $vendorResponse = json_encode($vendorResponse);
                    }

                    $logNotificationModel->vendor_response = $vendorResponse;

                    //CDaemon::log('vendor response:' . $vendorResponse);
                } catch (Exception $ex) {
                    //throw $ex;
                    $errCode++;
                    $errMessage = '[' . get_class($ex) . '] ' . $ex->getMessage() . ':' . $ex->getTraceAsString();
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
        if ($recipient instanceof CModel_Collection) {
            $firstModel = $recipient->first();
            if ($firstModel) {
                /** @var CModel $firstModel */
                $recipient = $recipient->pluck($firstModel->getKeyName());
            } else {
                $recipient = [];
            }
        }
        if ($recipient instanceof CModel) {
            $recipient = [$recipient->getKey()];
        }
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
        $model->org_id = $orgId;
        $model->channel = $this->channelName;
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
            $vendor = CF::config('notification.' . strtolower(cstr::snake($this->channelName)) . '.vendor');
        }

        return $vendor;
    }

    /**
     * @param mixed $data
     *
     * @return CNotification_MessageAbstract
     */
    public function createMessage($data) {
        $vendor = carr::get($data, 'vendor', carr::get($this->config, 'vendor', $this->getVendorName()));
        $vendorConfig = carr::get($data, 'vendor_config', carr::get($this->config, 'vendor_config'));
        if (!is_array($vendorConfig)) {
            $vendorConfig = CF::config('vendor.' . $vendor);
        }

        if (!is_array($vendorConfig)) {
            $vendorConfig = [];
        }

        return CNotification::manager()->createMessage($vendor, $vendorConfig, $data);
    }

    abstract protected function handleMessage($data, $logNotificationModel);
}
