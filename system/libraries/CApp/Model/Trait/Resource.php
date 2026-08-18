<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property      null|string                                               $createdby
 * @property      null|string                                               $updatedby
 * @property      null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $created
 * @property      null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $updated
 * @property      int                                                       $status
 * @property-read int                                                       $resource_id
 *
 * @deprecated since 1.4 dont use this anymore, all logic already implemented on CModel_Resource_ResourceTrait
 */
trait CApp_Model_Trait_Resource {
}
