<?php

namespace Psr\Clock;

use DateTimeImmutable;

interface ClockInterface {
    /**
     * Returns the current time as a DateTimeImmutable Object.
     *
     * @return DateTimeImmutable
     */
    public function now();
}
