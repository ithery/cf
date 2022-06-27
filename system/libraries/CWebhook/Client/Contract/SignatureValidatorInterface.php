<?php

interface CWebhook_Client_Contract_SignatureValidatorInterface {
    /**
     * @param CHTTP_Request          $request
     * @param CWebhook_Client_Config $config
     *
     * @return bool
     */
    public function isValid(CHTTP_Request $request, CWebhook_Client_Config $config);
}
