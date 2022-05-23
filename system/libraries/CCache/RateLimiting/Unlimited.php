<?php
class CCache_RateLimiting_Unlimited extends CCache_RateLimiting_GlobalLimit {
    /**
     * Create a new limit instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(PHP_INT_MAX);
    }
}
