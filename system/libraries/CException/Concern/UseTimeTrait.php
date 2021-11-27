<?php

trait CException_Concern_UseTimeTrait {
    /**
     * @var \CException_Contract_TimeInterface
     */
    public static $time;

    public static function useTime(CException_Contract_TimeInterface $time) {
        self::$time = $time;
    }

    /**
     * @return int
     */
    public function getCurrentTime() {
        $time = self::$time ?: new CException_SystemTime();

        return $time->getCurrentTime();
    }
}
