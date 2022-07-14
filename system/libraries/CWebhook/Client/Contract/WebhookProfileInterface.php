<?php

interface CWebhook_Client_Contract_WebhookProfileInterface {
    /**
     * @param CHTTP_Request $request
     *
     * @return bool
     */
    public function shouldProcess(CHTTP_Request $request);
}
