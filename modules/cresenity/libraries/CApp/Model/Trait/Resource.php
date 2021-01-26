<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 12:22:54 AM
 */
trait CApp_Model_Trait_Resource {
    use CApp_Model_Trait_Resource_IsSorted,
        CApp_Model_Trait_Resource_CustomResourceProperties;
}
