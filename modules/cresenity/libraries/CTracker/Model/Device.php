<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 2:23:28 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Device extends CTracker_Model {

    protected $table = 'log_device';
    protected $fillable = [
        'org_id',
        'kind',
        'model',
        'platform',
        'platform_version',
        'is_mobile',
    ];

}
