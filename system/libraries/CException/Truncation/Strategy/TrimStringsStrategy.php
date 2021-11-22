<?php

class CException_Truncation_Strategy_TrimStringsStrategy extends CException_Truncation_StrategyAbstract {
    public static function thresholds() {
        return [1024, 512, 256];
    }

    /**
     * @param array $payload
     *
     * @return array
     */
    public function execute(array $payload) {
        foreach (static::thresholds() as $threshold) {
            if (!$this->reportTrimmer->needsToBeTrimmed($payload)) {
                break;
            }

            $payload = $this->trimPayloadString($payload, $threshold);
        }

        return $payload;
    }

    /**
     * @param array $payload
     * @param int   $threshold
     *
     * @return array
     */
    protected function trimPayloadString(array $payload, $threshold) {
        array_walk_recursive($payload, function (&$value) use ($threshold) {
            if (is_string($value) && strlen($value) > $threshold) {
                $value = substr($value, 0, $threshold);
            }
        });

        return $payload;
    }
}
