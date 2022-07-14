<?php

class CWebhook_Client_SignatureValidator_DefaultSignatureValidator implements CWebhook_Client_Contract_SignatureValidatorInterface {
    public function isValid(CHTTP_Request $request, CWebhook_Client_Config $config): bool {
        $signature = $request->header($config->signatureHeaderName);

        if (!$signature) {
            return false;
        }

        $signingSecret = $config->signingSecret;

        if (empty($signingSecret)) {
            throw CWebhook_Client_Exception_InvalidConfigException::signingSecretNotSet();
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $signingSecret);

        return hash_equals($signature, $computedSignature);
    }
}
