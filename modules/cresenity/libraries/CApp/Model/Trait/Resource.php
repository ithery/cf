<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 12:22:54 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_Resource {

    use CApp_Model_Trait_Resource_IsSorted,
        CApp_Model_Trait_Resource_CustomResourceProperties;
}
