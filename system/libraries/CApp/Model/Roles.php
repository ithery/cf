<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property-read int            $role_id
 * @property      int            $depth
 * @property      null|string    $createdby
 * @property      null|string    $updatedby
 * @property      CCarbon|string $created
 * @property      CCarbon|string $updated
 * @property      int            $status
 */
class CApp_Model_Roles extends CApp_Model implements CApp_Auth_Contract_RoleInterface {
    use CApp_Model_Trait_Roles;
}
