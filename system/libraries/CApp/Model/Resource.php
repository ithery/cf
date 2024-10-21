<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property-read int                         $resource_id
 * @property      null|int                    $org_id
 * @property      null|string                 $model_type
 * @property      null|int                    $model_id
 * @property      null|string                 $collection_name
 * @property      null|string                 $name
 * @property      null|string                 $file_name
 * @property      null|string                 $mime_type
 * @property      null|string                 $disk
 * @property      null|int                    $size
 * @property      null|string                 $manipulations
 * @property      null|string                 $custom_properties
 * @property      null|string                 $responsive_images
 * @property      null|int                    $order_column
 * @property      null|int                    $is_active
 * @property      null|CCarbon|\Carbon\Carbon $deleted
 * @property      null|string                 $deletedby
 */
class CApp_Model_Resource extends CApp_Model implements CModel_Resource_ResourceInterface {
    use CModel_Resource_ResourceTrait;

    protected $table = 'resource';

    protected $guarded = ['resource_id'];

    protected $casts = [
        'manipulations' => 'array',
        'custom_properties' => 'array',
        'responsive_images' => 'array',
    ];
}
