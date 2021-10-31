<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 4:38:22 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CDebug_Bar_Interface_RequestIdGeneratorInterface {

    /**
     * Generates a unique id for the current request.  If called repeatedly, a new unique id must
     * always be returned on each call to generate() - even across different object instances.
     *
     * To avoid any potential confusion in ID --> value maps, the returned value must be
     * guaranteed to not be all-numeric.
     *
     * @return string
     */
    function generate();
}
