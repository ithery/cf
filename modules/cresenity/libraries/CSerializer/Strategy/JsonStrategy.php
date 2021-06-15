<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 23, 2018, 12:05:01 AM
 */

/**
 * Class JsonStrategy.
 */
class CSerializer_Strategy_JsonStrategy implements CSerializer_StrategyInterface {
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value) {
        return \json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function unserialize($value) {
        return \json_decode($value, true);
    }
}
