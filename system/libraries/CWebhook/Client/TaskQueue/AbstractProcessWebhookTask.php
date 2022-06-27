<?php

abstract class CWebhook_Client_TaskQueue_AbstractProcessWebhookTask extends CQueue_AbstractTask {
    public $webhookCallModel;

    public function __construct(
        $webhookCallModel
    ) {
        $this->webhookCallModel = $webhookCallModel;
    }
}
