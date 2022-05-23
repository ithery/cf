<?php

/**
 * @internal
 */
class CCarbon_BusinessDay_BusinessMonth {
    /**
     * @var \Carbon\Carbon|\CCarbon_BusinessDay
     */
    protected $start;

    /**
     * @var \Carbon\Carbon|\CCarbon_BusinessDay
     */
    protected $end;

    /**
     * @param \Carbon\Carbon|\CCarbon_BusinessDay|string $date
     * @param string                                     $carbonClass
     */
    public function __construct($date, $carbonClass) {
        if (is_string($date)) {
            $date = $carbonClass::parse($date);
        } elseif (!is_a($date, $carbonClass)) {
            $date = $carbonClass::instance($date ?: $carbonClass::now());
        }

        $this->start = $date->copy()->startOfMonth();
        $this->end = $date->copy()->endOfMonth();
    }

    /**
     * @return \Carbon\Carbon|\CCarbon_BusinessDay
     */
    public function getStart() {
        return $this->start;
    }

    /**
     * @return \Carbon\Carbon|\CCarbon_BusinessDay
     */
    public function getEnd() {
        return $this->end;
    }
}
