<?php

interface CVendor_Firebase_JWT_Contract_ExpirableInterface {
    /**
     * @param DateTimeImmutable $time
     *
     * @return self
     */
    public function withExpirationTime(DateTimeImmutable $time);

    /**
     * @param DateTimeInterface $now
     *
     * @return bool
     */
    public function isExpiredAt(DateTimeInterface $now);

    /**
     * @return DateTimeImmutable
     */
    public function expiresAt();
}
