<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 11:16:18 PM
 */
interface CInterface_Arrayable {
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}
