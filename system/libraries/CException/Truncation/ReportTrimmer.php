<?php

class CException_Truncation_ReportTrimmer {
    protected static $maxPayloadSize = 524288;

    protected $strategies = [
        CException_Truncation_Strategy_TrimStringsStrategy::class,
        CException_Truncation_Strategy_TrimContextItemsStrategy::class,
    ];

    /**
     * @param array $payload
     *
     * @return array
     */
    public function trim(array $payload) {
        foreach ($this->strategies as $strategy) {
            if (!$this->needsToBeTrimmed($payload)) {
                break;
            }

            $payload = (new $strategy($this))->execute($payload);
        }

        return $payload;
    }

    /**
     * @param array $payload
     *
     * @return bool
     */
    public function needsToBeTrimmed(array $payload) {
        return strlen(json_encode($payload)) > self::getMaxPayloadSize();
    }

    /**
     * @return int
     */
    public static function getMaxPayloadSize() {
        return self::$maxPayloadSize;
    }

    /**
     * @param int $maxPayloadSize
     *
     * @return void
     */
    public static function setMaxPayloadSize($maxPayloadSize) {
        self::$maxPayloadSize = $maxPayloadSize;
    }
}
