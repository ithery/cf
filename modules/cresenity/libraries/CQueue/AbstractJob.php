<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 2:18:49 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CQueue_AbstractJob {

    use CQueue_Trait_DispatchableTrait;
}
