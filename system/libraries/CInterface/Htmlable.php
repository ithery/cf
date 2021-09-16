<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
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
