<?php

class CWebhook_Server_Exception_InvalidSignerException extends Exception {
    /**
     * @param string $invalidClassName
     *
     * @return self
     */
    public static function doesNotImplementSigner($invalidClassName) {
        $signerInterface = CWebhook_Server_Contract_SignerInterface::class;

        return new static("`{$invalidClassName}` is not a valid signer class because it does not implement `${signerInterface}`");
    }
}
