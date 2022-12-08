<?php

final class CMetric_TimeControl {
    private DateTimeInterface $started;

    public function __construct(DateTimeInterface $started) {
        $this->started = $started;
    }

    public function sinceStartTo(DateTimeInterface $time): float {
        return round(((float) $time->format('U.u') - (float) $this->started->format('U.u')) * 1000, 2);
    }
}
