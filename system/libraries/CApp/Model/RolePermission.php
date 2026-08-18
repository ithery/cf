<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property      string $nav
 * @property      string $name
 * @property      int    $app_id
 * @property-read int    $role_permission_id
 */
class CApp_Model_RolePermission extends CApp_Model {
    use CApp_Model_Trait_RolePermission;
}
