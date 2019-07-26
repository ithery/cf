<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 1, 2019, 11:37:14 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Model_Resource extends CApp_Model implements CApp_Model_Interface_ResourceInterface {

    use CApp_Model_Trait_Resource,
        CModel_Resource_ResourceTrait;

    protected $table = 'resource';
    protected $guarded = ['resource_id'];
    protected $casts = [
        'manipulations' => 'array',
        'custom_properties' => 'array',
        'responsive_images' => 'array',
    ];

}
