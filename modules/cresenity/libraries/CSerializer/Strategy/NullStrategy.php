<?php

/**
 * Class NullStrategy.
 */
class CSerializer_Strategy_NullStrategy implements CSerializer_StrategyInterface {
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value) {
        return $value;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    public function unserialize($value) {
        return $value;
    }
}
