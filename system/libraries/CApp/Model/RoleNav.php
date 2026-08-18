<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property      string $nav
 * @property      int    $app_id
 * @property-read int    $role_nav_id
 */
class CApp_Model_RoleNav extends CApp_Model {
    use CApp_Model_Trait_RoleNav;
}
