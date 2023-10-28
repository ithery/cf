<?php
/** @mixin CPeriod */
trait CPeriod_Trait_OperationTrait {
    /**
     * @param CPeriod $period
     *
     * @return null|static
     */
    public function gap(CPeriod $period) {
        $this->ensurePrecisionMatches($period);

        if ($this->overlapsWith($period)) {
            return null;
        }

        if ($this->touchesWith($period)) {
            return null;
        }

        if ($this->includedStart() >= $period->includedEnd()) {
            return static::make(
                $period->includedEnd()->add($this->interval),
                $this->includedStart()->sub($this->interval),
                $this->precision()
            );
        }

        return static::make(
            $this->includedEnd()->add($this->interval),
            $period->includedStart()->sub($this->interval),
            $this->precision()
        );
    }

    /**
     * @param CPeriod ...$others
     *
     * @return null|static
     */
    public function overlap(CPeriod ...$others) {
        if (count($others) === 0) {
            return null;
        } elseif (count($others) > 1) {
            return $this->overlapAll(...$others);
        } else {
            $other = $others[0];
        }

        $this->ensurePrecisionMatches($other);

        $includedStart = $this->includedStart() > $other->includedStart()
            ? $this->includedStart()
            : $other->includedStart();

        $includedEnd = $this->includedEnd() < $other->includedEnd()
            ? $this->includedEnd()
            : $other->includedEnd();

        if ($includedStart > $includedEnd) {
            return null;
        }

        return CPeriod_Factory::makeWithBoundaries(
            static::class,
            $includedStart,
            $includedEnd,
            $this->precision(),
            $this->boundaries()
        );
    }

    /**
     * @param CPeriod ...$periods
     *
     * @return null|static
     */
    protected function overlapAll(CPeriod ...$periods) {
        $overlap = clone $this;

        if (!count($periods)) {
            return $overlap;
        }

        foreach ($periods as $period) {
            $overlap = $overlap->overlap($period);

            if ($overlap === null) {
                return null;
            }
        }

        return $overlap;
    }

    /**
     * @param CPeriod ...$others
     *
     * @return CPeriod_Collection|static[]
     */
    public function overlapAny(CPeriod ...$others) {
        $overlapCollection = new CPeriod_Collection();

        foreach ($others as $period) {
            $overlap = $this->overlap($period);

            if ($overlap === null) {
                continue;
            }

            $overlapCollection[] = $overlap;
        }

        return $overlapCollection;
    }

    /**
     * @param \CPeriod|iterable $other
     *
     * @return \CPeriod_Collection|static[]
     */
    public function subtract(CPeriod ...$others) {
        if (count($others) === 0) {
            return CPeriod_Collection::make($this);
        } elseif (count($others) > 1) {
            return $this->subtractAll(...$others);
        } else {
            $other = $others[0];
        }

        $this->ensurePrecisionMatches($other);

        $collection = new CPeriod_Collection();

        if (!$this->overlapsWith($other)) {
            $collection[] = $this;

            return $collection;
        }

        if ($this->includedStart() < $other->includedStart()) {
            $collection[] = CPeriod_Factory::makeWithBoundaries(
                $this->includedStart(),
                $other->includedStart()->sub($this->interval),
                $this->precision(),
                $this->boundaries()
            );
        }

        if ($this->includedEnd() > $other->includedEnd()) {
            $collection[] = CPeriod_Factory::makeWithBoundaries(
                $other->includedEnd()->add($this->interval),
                $this->includedEnd(),
                $this->precision(),
                $this->boundaries()
            );
        }

        return $collection;
    }

    /**
     * @param CPeriod ...$others
     *
     * @return CPeriod_Collection
     */
    protected function subtractAll(CPeriod ...$others) {
        $subtractions = [];

        foreach ($others as $other) {
            $subtractions[] = $this->subtract($other);
        }

        return (new CPeriod_Collection($this))->overlapAll(...$subtractions);
    }

    /**
     * @param \CPeriod $other
     *
     * @return \CPeriod_Collection|static[]
     */
    public function diffSymmetric(CPeriod $other) {
        $this->ensurePrecisionMatches($other);

        $periodCollection = new CPeriod_Collection();

        if (!$this->overlapsWith($other)) {
            $periodCollection[] = clone $this;
            $periodCollection[] = clone $other;

            return $periodCollection;
        }

        $boundaries = (new CPeriod_Collection($this, $other))->boundaries();

        $overlap = $this->overlap($other);

        return $boundaries->subtract($overlap);
    }

    /**
     * @return static
     */
    public function renew() {
        $length = $this->includedStart->diff($this->includedEnd);

        $start = $this->includedEnd->add($this->interval);

        $end = $start->add($length);

        return static::make($start, $end, $this->precision, $this->boundaries);
    }
}
