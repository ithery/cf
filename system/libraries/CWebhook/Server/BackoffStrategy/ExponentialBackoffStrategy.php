<?php

class CWebhook_Server_BackoffStrategy_ExponentialBackoffStrategy implements CWebhook_Server_Contract_BackoffStrategyInterface {
    /**
     * @param int $attempt
     *
     * @return int
     */
    public function waitInSecondsAfterAttempt($attempt) {
        if ($attempt > 4) {
            return 100000;
        }

        return 10 ** $attempt;
    }
}
