<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 3:02:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CAjax_Engine_DataTable_ProcessorInterface {

    public function __construct(CAjax_Engine $engine);

    public function process();
}
