<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 2:42:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_GeoIp extends CTracker_Model {

    use CModel_Tracker_TrackerGeoIpTrait;
    
    protected $table = 'log_geoip';
    protected $fillable = [
        'org_id',
        'country_code',
        'country_code3',
        'country_name',
        'region',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'area_code',
        'dma_code',
        'metro_code',
        'continent_code',
    ];

}
