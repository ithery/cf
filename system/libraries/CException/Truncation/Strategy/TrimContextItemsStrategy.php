<?php

class CException_Truncation_Strategy_TrimContextItemsStrategy extends CException_Truncation_StrategyAbstract {
    public static function thresholds() {
        return [100, 50, 25, 10];
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

            $payload['context'] = $this->iterateContextItems($payload['context'], $threshold);
        }

        return $payload;
    }

    protected function iterateContextItems(array $contextItems, $threshold) {
        array_walk($contextItems, [$this, 'trimContextItems'], $threshold);

        return $contextItems;
    }

    protected function trimContextItems(&$value, $key, $threshold) {
        if (is_array($value)) {
            if (count($value) > $threshold) {
                $value = array_slice($value, $threshold * -1, $threshold);
            }

            array_walk($value, [$this, 'trimContextItems'], $threshold);
        }

        return $value;
    }
}
