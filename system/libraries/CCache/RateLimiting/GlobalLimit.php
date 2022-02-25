<?php
class CCache_RateLimiting_GlobalLimit extends CCache_RateLimiting_Limit {
    /**
     * Create a new limit instance.
     *
     * @param int $maxAttempts
     * @param int $decayMinutes
     *
     * @return void
     */
    public function __construct($maxAttempts, $decayMinutes = 1) {
        parent::__construct('', $maxAttempts, $decayMinutes);
    }
}
