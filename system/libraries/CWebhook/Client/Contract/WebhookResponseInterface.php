<?php
interface CWebhook_Client_Contract_WebhookResponseInterface {
    /**
     * @param CHTTP_Request          $request
     * @param CWebhook_Client_Config $config
     *
     * @return CHTTP_Response
     */
    public function respondToValidWebhook(CHTTP_Request $request, CWebhook_Client_Config $config);
}
