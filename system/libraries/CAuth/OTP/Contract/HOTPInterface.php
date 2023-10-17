<?php

interface CAuth_OTP_Contract_HOTPInterface extends CAuth_OTP_Contract_OTPInterface {
    public const DEFAULT_COUNTER = 0;

    /**
     * The initial counter (a positive integer).
     */
    public function getCounter(): int;

    /**
     * Create a new HOTP object.
     *
     * If the secret is null, a random 64 bytes secret will be generated.
     *
     * @param null|string $secret
     * @param int         $counter
     * @param string      $digest
     * @param int         $digits
     *
     * @deprecated Deprecated since v11.1, use ::createFromSecret or ::generate instead
     */
    public static function create(
        string $secret = null,
        int $counter = 0,
        string $digest = 'sha1',
        int $digits = 6
    ): self;

    public function setCounter(int $counter): void;
}
