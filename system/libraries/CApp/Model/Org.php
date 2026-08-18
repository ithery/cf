<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property-read int                         $org_id
 * @property      null|string                 $code
 * @property      null|string                 $domain
 * @property      null|string                 $name
 * @property      null|string                 $address
 * @property      null|string                 $city
 * @property      null|string                 $email
 * @property      null|string                 $phone
 * @property      null|string                 $contact_person
 * @property      null|string                 $auth_type
 * @property      null|CCarbon|\Carbon\Carbon $deleted
 * @property      null|string                 $deletedby
 */
class CApp_Model_Org extends CApp_Model {
    use CApp_Model_Trait_Org;
}
