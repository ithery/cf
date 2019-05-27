<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 1, 2019, 11:39:29 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CInterface_Htmlable {

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml();
}
