<?php

interface CAuth_OTP_Contract_TOTPInterface extends CAuth_OTP_Contract_OTPInterface {
    public const DEFAULT_PERIOD = 30;

    public const DEFAULT_EPOCH = 0;

    /**
     * Create a new TOTP object.
     *
     * If the secret is null, a random 64 bytes secret will be generated.
     *
     * @param null|string      $secret
     * @param int              $period
     * @param non-empty-string $digest
     * @param int              $digits
     *
     * @deprecated Deprecated since v11.1, use ::createFromSecret or ::generate instead
     */
    public static function create(
        string $secret = null,
        int $period = self::DEFAULT_PERIOD,
        string $digest = self::DEFAULT_DIGEST,
        int $digits = self::DEFAULT_DIGITS
    ): self;

    public function setPeriod(int $period): void;

    public function setEpoch(int $epoch): void;

    /**
     * Return the TOTP at the current time.
     *
     * @return non-empty-string
     */
    public function now(): string;

    /**
     * Get the period of time for OTP generation (a non-null positive integer, in second).
     */
    public function getPeriod(): int;

    public function expiresIn(): int;

    public function getEpoch(): int;
}
