<?php

class CWebhook_Client_WebhookProfile_ProcessEverythingWebhookProfile implements CWebhook_Client_Contract_WebhookProfileInterface {
    /**
     * @param CHTTP_Request $request
     *
     * @return bool
     */
    public function shouldProcess(CHTTP_Request $request) {
        return true;
    }
}
