<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property-read int     $role_id
 * @property      int     $depth
 * @property      string  $createdby
 * @property      string  $updatedby
 * @property      CCarbon $created
 * @property      CCarbon $updated
 * @property      int     $status
 */
class CApp_Model_Roles extends CApp_Model implements CApp_Auth_Contract_RoleInterface {
    use CApp_Model_Trait_Roles;
}
