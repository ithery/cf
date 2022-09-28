<?php
trait CPeriod_OpeningHours_Trait_DiffTrait {
    /**
     * @param string            $stateCheckMethod
     * @param string            $nextDateMethod
     * @param string            $skipDateMethod
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return float
     */
    private function diffInSeconds($stateCheckMethod, $nextDateMethod, $skipDateMethod, DateTimeInterface $startDate, DateTimeInterface $endDate) {
        $time = 0;

        if ($endDate < $startDate) {
            return -$this->diffInSeconds($stateCheckMethod, $nextDateMethod, $skipDateMethod, $endDate, $startDate);
        }

        $date = $startDate;

        while ($date < $endDate) {
            if ($this->$stateCheckMethod($date)) {
                $date = $this->$skipDateMethod($date);

                continue;
            }

            $nextDate = min($endDate, $this->$nextDateMethod($date));
            $time += floatval($nextDate->format('U.u')) - floatval($date->format('U.u'));
            $date = $nextDate;
        }

        return $time;
    }

    /**
     * Return the amount of open time (number of seconds as a floating number) between 2 dates/times.
     *
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return float
     */
    public function diffInOpenSeconds(DateTimeInterface $startDate, DateTimeInterface $endDate) {
        return $this->diffInSeconds('isClosedAt', 'nextClose', 'nextOpen', $startDate, $endDate);
    }

    /**
     * Return the amount of open time (number of minutes as a floating number) between 2 dates/times.
     *
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return float
     */
    public function diffInOpenMinutes(DateTimeInterface $startDate, DateTimeInterface $endDate) {
        return $this->diffInOpenSeconds($startDate, $endDate) / 60;
    }

    /**
     * Return the amount of open time (number of hours as a floating number) between 2 dates/times.
     *
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return float
     */
    public function diffInOpenHours(DateTimeInterface $startDate, DateTimeInterface $endDate) {
        return $this->diffInOpenMinutes($startDate, $endDate) / 60;
    }

    /**
     * Return the amount of closed time (number of seconds as a floating number) between 2 dates/times.
     *
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return float
     */
    public function diffInClosedSeconds(DateTimeInterface $startDate, DateTimeInterface $endDate) {
        return $this->diffInSeconds('isOpenAt', 'nextOpen', 'nextClose', $startDate, $endDate);
    }

    /**
     * Return the amount of closed time (number of minutes as a floating number) between 2 dates/times.
     *
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return float
     */
    public function diffInClosedMinutes(DateTimeInterface $startDate, DateTimeInterface $endDate) {
        return $this->diffInClosedSeconds($startDate, $endDate) / 60;
    }

    /**
     * Return the amount of closed time (number of hours as a floating number) between 2 dates/times.
     *
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return float
     */
    public function diffInClosedHours(DateTimeInterface $startDate, DateTimeInterface $endDate) {
        return $this->diffInClosedMinutes($startDate, $endDate) / 60;
    }
}
