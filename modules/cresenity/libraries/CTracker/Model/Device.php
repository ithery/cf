<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 2:23:28 AM
 */
class CTracker_Model_Device extends CTracker_Model {
    use CModel_Tracker_TrackerDeviceTrait;

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
