<?php

interface CWebhook_Server_Contract_BackoffStrategyInterface {
    /**
     * @param int $attempt
     *
     * @return int
     */
    public function waitInSecondsAfterAttempt($attempt);
}
