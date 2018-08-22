<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 23, 2018, 12:05:38 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CSerializer_StrategyInterface {

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value);

    /**
     * @param $value
     *
     * @return array
     */
    public function unserialize($value);
}
