<?php

class CPeriod_Collection implements ArrayAccess, Iterator, Countable {
    use CPeriod_Trait_IterableImplementationTrait;

    /**
     * @var \CPeriod[]
     */
    protected array $periods;

    /**
     * @param CPeriod ...$periods
     *
     * @return static
     */
    public static function make(CPeriod ...$periods) {
        return new static(...$periods);
    }

    public function __construct(CPeriod ...$periods) {
        $this->periods = $periods;
    }

    /**
     * @return CPeriod
     */
    public function current() {
        return $this->periods[$this->position];
    }

    /**
     * @param CPeriod_Collection ...$others
     *
     * @return CPeriod_Collection
     */
    public function overlapAll(CPeriod_Collection ...$others) {
        $overlap = clone $this;

        foreach ($others as $other) {
            $overlap = $overlap->overlap($other);
        }

        return $overlap;
    }

    /**
     * @return null|CPeriod
     */
    public function boundaries() {
        $start = null;
        $end = null;

        foreach ($this as $period) {
            if ($start === null || $start > $period->includedStart()) {
                $start = $period->includedStart();
            }

            if ($end === null || $end < $period->includedEnd()) {
                $end = $period->includedEnd();
            }
        }

        if (!$start || !$end) {
            return null;
        }

        list($firstPeriod) = $this->periods;

        return CPeriod::make(
            $start,
            $end,
            $firstPeriod->precision(),
            CPeriod_Boundaries::EXCLUDE_NONE()
        );
    }

    /**
     * @return static
     */
    public function gaps() {
        $boundaries = $this->boundaries();

        if (!$boundaries) {
            return static::make();
        }

        return $boundaries->subtract(...$this);
    }

    /**
     * @param CPeriod $intersection
     *
     * @return static
     */
    public function intersect(CPeriod $intersection) {
        $intersected = static::make();

        foreach ($this as $period) {
            $overlap = $intersection->overlap($period);

            if ($overlap === null) {
                continue;
            }

            $intersected[] = $overlap;
        }

        return $intersected;
    }

    /**
     * @param CPeriod $intersection
     *
     * @return static
     */
    public function add(Period ...$periods) {
        $collection = clone $this;

        foreach ($periods as $period) {
            $collection[] = $period;
        }

        return $collection;
    }

    /**
     * @param Closure $closure
     *
     * @return static
     */
    public function map(Closure $closure) {
        $collection = clone $this;

        foreach ($collection->periods as $key => $period) {
            $collection->periods[$key] = $closure($period);
        }

        return $collection;
    }

    /**
     * @param Closure    $closure
     * @param null|mixed $initial
     *
     * @return mixed
     */
    public function reduce(Closure $closure, $initial = null) {
        $carry = $initial;

        foreach ($this as $period) {
            $carry = $closure($carry, $period);
        }

        return $carry;
    }

    /**
     * @param Closure $closure
     *
     * @return static
     */
    public function filter(Closure $closure) {
        $collection = clone $this;

        $collection->periods = array_values(array_filter($collection->periods, $closure));

        return $collection;
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return count($this->periods) === 0;
    }

    /**
     * @param CPeriod_Collection|CPeriod $others
     *
     * @return static
     */
    public function subtract($others) {
        if ($others instanceof CPeriod) {
            $others = new static($others);
        }

        if ($others->count() === 0) {
            return clone $this;
        }

        $collection = new static();

        foreach ($this as $period) {
            $collection = $collection->add(...$period->subtract(...$others));
        }

        return $collection;
    }

    /**
     * @param CPeriod_Collection $others
     *
     * @return CPeriod_Collection
     */
    private function overlap(CPeriod_Collection $others) {
        $overlaps = new CPeriod_Collection();

        foreach ($this as $period) {
            foreach ($others as $other) {
                if (!$period->overlap($other)) {
                    continue;
                }

                $overlaps[] = $period->overlap($other);
            }
        }

        return $overlaps;
    }

    /**
     * @return CPeriod_Collection
     */
    public function unique() {
        $uniquePeriods = [];
        foreach ($this->periods as $period) {
            $uniquePeriods[$period->asString()] = $period;
        }

        return new static(...array_values($uniquePeriods));
    }

    /**
     * @return CPeriod_Collection
     */
    public function sort() {
        $collection = clone $this;

        usort($collection->periods, static function (CPeriod $a, CPeriod $b) {
            return c::spaceshipOperator($a->includedStart(), $b->includedStart());
        });

        return $collection;
    }
}
