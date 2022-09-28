<?php
trait CPeriod_OpeningHours_Trait_RangeFinderTrait {
    protected function findRangeInFreeTime(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        if ($time->isBefore($timeRange->start())) {
            return $timeRange;
        }
    }

    protected function findOpenInFreeTime(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        $range = $this->findRangeInFreeTime($time, $timeRange);

        if ($range) {
            return $range->start();
        }
    }

    protected function findOpenRangeInWorkingHours(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        if ($time->isAfter($timeRange->start())) {
            return $timeRange;
        }
    }

    protected function findOpenInWorkingHours(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        $range = $this->findOpenRangeInWorkingHours($time, $timeRange);

        if ($range) {
            return $range->start();
        }
    }

    protected function findCloseInWorkingHours(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        if ($timeRange->containsTime($time)) {
            return $timeRange->end();
        }
    }

    protected function findCloseRangeInWorkingHours(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        if ($timeRange->containsTime($time)) {
            return $timeRange;
        }
    }

    protected function findCloseInFreeTime(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        $range = $this->findRangeInFreeTime($time, $timeRange);

        if ($range) {
            return $range->end();
        }
    }

    protected function findPreviousRangeInFreeTime(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        if ($time->isAfter($timeRange->end())) {
            return $timeRange;
        }
    }

    protected function findPreviousOpenInFreeTime(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        $range = $this->findPreviousRangeInFreeTime($time, $timeRange);

        if ($range) {
            return $range->start();
        }
    }

    protected function findPreviousCloseInWorkingHours(CPeriod_OpeningHours_Time $time, CPeriod_OpeningHours_TimeRange $timeRange) {
        $end = $timeRange->end();

        if ($time->isAfter($end)) {
            return $end;
        }
    }
}
