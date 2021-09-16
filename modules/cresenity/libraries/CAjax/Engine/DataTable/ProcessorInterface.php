<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 8, 2018, 3:02:46 AM
 */
interface CAjax_Engine_DataTable_ProcessorInterface {
    public function __construct(CAjax_Engine $engine);

    public function process();
}
