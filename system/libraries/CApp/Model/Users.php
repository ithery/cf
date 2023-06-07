<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property string  $createdby
 * @property string  $updatedby
 * @property CCarbon $created
 * @property CCarbon $updated
 * @property int     $status
 */
class CApp_Model_Users extends CApp_Model implements CAuth_AuthenticatableInterface, CAuth_Contract_ImpersonateableInterface {
    use CApp_Model_Trait_Users;
    use CAuth_Concern_AuthenticatableTrait,
        CAuth_Concern_AuthorizableTrait;
    use CAuth_Concern_ImpersonateableTrait;
}
