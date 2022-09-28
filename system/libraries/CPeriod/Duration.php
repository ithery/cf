<?php

class CPeriod_Duration {
    /**
     * @var CPeriod
     */
    private $period;

    public function __construct($period) {
        $this->period = $period;
    }

    /**
     * @param CPeriod_Duration $other
     *
     * @return bool
     */
    public function equals(CPeriod_Duration $other) {
        return $this->startAndEndDatesAreTheSameAs($other)
            || $this->includedStartAndEndDatesAreTheSameAs($other)
            || $this->numberOfDaysIsTheSameAs($other)
            || $this->compareTo($other) === 0;
    }

    /**
     * @param CPeriod_Duration $other
     *
     * @return bool
     */
    public function isLargerThan(CPeriod_Duration $other) {
        return $this->compareTo($other) === 1;
    }

    /**
     * @param CPeriod_Duration $other
     *
     * @return bool
     */
    public function isSmallerThan(CPeriod_Duration $other) {
        return $this->compareTo($other) === -1;
    }

    /**
     * @param CPeriod_Duration $other
     *
     * @return int
     */
    public function compareTo(CPeriod_Duration $other) {
        $now = new DateTimeImmutable('@' . time()); // Ensure a TimeZone independent instance

        $here = $this->period->includedEnd()->diff($this->period->includedStart(), true);
        $there = $other->period->includedEnd()->diff($other->period->includedStart(), true);

        return c::spaceshipOperator($now->add($here)->getTimestamp(), $now->add($there)->getTimestamp());
    }

    /**
     * @param CPeriod_Duration $other
     *
     * @return bool
     */
    private function startAndEndDatesAreTheSameAs(CPeriod_Duration $other) {
        return $this->period->start() == $other->period->start()
            && $this->period->end() == $other->period->end();
    }

    /**
     * @param CPeriod_Duration $other
     *
     * @return bool
     */
    private function includedStartAndEndDatesAreTheSameAs(CPeriod_Duration $other) {
        return $this->period->includedStart() == $other->period->includedStart()
            && $this->period->includedEnd() == $other->period->includedEnd();
    }

    /**
     * @param CPeriod_Duration $other
     *
     * @return void
     */
    private function numberOfDaysIsTheSameAs(CPeriod_Duration $other) {
        $here = $this->period->includedEnd()->diff($this->period->includedStart(), true);
        $there = $other->period->includedEnd()->diff($other->period->includedStart(), true);

        return $here->format('%a') === $there->format('%a');
    }
}
