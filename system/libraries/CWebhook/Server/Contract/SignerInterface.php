<?php

interface CWebhook_Server_Contract_SignerInterface {
    /**
     * @return string
     */
    public function signatureHeaderName();

    /**
     * @param string $webhookUrl
     * @param array  $payload
     * @param string $secret
     *
     * @return string
     */
    public function calculateSignature($webhookUrl, array $payload, $secret);
}
