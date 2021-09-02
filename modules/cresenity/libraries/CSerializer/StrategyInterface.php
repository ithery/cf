<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 23, 2018, 12:05:38 AM
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
