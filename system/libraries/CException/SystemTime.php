<?php

class CException_SystemTime implements CException_Contract_TimeInterface {
    public function getCurrentTime() {
        if (class_exists('DateTimeImmutable')) {
            return (new DateTimeImmutable())->getTimestamp();
        }

        return time();
    }
}
