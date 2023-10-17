<?php

use Psr\Clock\ClockInterface;

/**
 * @internal
 */
final class CAuth_OTP_InternalClock implements ClockInterface {
    /**
     * @return DateTimeImmutable
     */
    public function now() {
        return new DateTimeImmutable();
    }
}
