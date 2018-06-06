<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 11:16:18 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CInterface_Arrayable {

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}
