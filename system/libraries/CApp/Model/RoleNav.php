<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property string  $createdby
 * @property string  $updatedby
 * @property CCarbon $created
 * @property CCarbon $updated
 * @property int     $status
 */
class CApp_Model_RoleNav extends CApp_Model {
    use CApp_Model_Trait_RoleNav;
}
