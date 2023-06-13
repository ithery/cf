<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property      string $key
 * @property      int    $counter
 * @property      int    $org_id
 * @property-read int    $sys_counter_id
 */
class CApp_Model_SysCounter extends CApp_Model {
    use CApp_Model_Trait_SysCounter;
}
