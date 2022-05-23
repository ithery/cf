<?php

trait CVendor_Firebase_JWT_Concern_ExpirableTrait {
    private DateTimeImmutable $expirationTime;

    /**
     * @param DateTimeImmutable $expirationTime
     *
     * @return self
     */
    public function withExpirationTime(DateTimeImmutable $expirationTime) {
        $expirable = clone $this;
        $expirable->expirationTime = $expirationTime;

        return $expirable;
    }

    /**
     * @param DateTimeInterface $now
     *
     * @return bool
     */
    public function isExpiredAt(DateTimeInterface $now) {
        return $this->expirationTime < $now;
    }

    /**
     * @return DateTimeImmutable
     */
    public function expiresAt() {
        return $this->expirationTime;
    }
}
