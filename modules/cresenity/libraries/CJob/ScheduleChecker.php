<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CJob_ScheduleChecker {

    /**
     * @param string|callable $schedule
     * @return bool
     */
    public function isDue($schedule) {
        if (is_callable($schedule)) {
            return call_user_func($schedule);
        }
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $schedule);
        if ($dateTime !== false) {
            return $dateTime->format('Y-m-d H:i') == (date('Y-m-d H:i'));
        }
        return CJob_CronExpression::factory((string) $schedule)->isDue();
    }

}
