<?php

class CWebhook_Server_Exception_InvalidBackoffStrategyException extends Exception {
    /**
     * @param string $invalidBackoffStrategyClass
     *
     * @return self
     */
    public static function doesNotExtendBackoffStrategy($invalidBackoffStrategyClass) {
        $backoffStrategyInterface = CWebhook_Server_Contract_BackoffStrategyInterface::class;

        return new static("`{$invalidBackoffStrategyClass}` is not a valid backoff strategy class because it does not implement `${backoffStrategyInterface}`");
    }
}
