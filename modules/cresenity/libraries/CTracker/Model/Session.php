<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 2:19:58 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Session extends CTracker_Model {

    use CModel_Tracker_TrackerSessionTrait;

    protected $table = 'log_session';
    protected $fillable = [
        'org_id',
        'uuid',
        'user_id',
        'log_device_id',
        'log_language_id',
        'log_agent_id',
        'client_ip',
        'log_cookie_id',
        'log_referer_id',
        'log_geoip_id',
        'is_robot',
    ];

}
