<?php

class CJob_ScheduleChecker {
    /**
     * @param string|callable $schedule
     *
     * @return bool
     */
    public function isDue($schedule) {
        if (is_callable($schedule)) {
            return call_user_func($schedule);
        }
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $schedule);
        if ($dateTime !== false) {
            return strtotime($dateTime->format('Y-m-d H:i')) <= strtotime((date('Y-m-d H:i')));
        }
        return CJob_CronExpression::factory((string) $schedule)->isDue();
    }
}
