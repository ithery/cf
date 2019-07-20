<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:16:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Referer extends CTracker_Model {

    use CModel_Tracker_TrackerRefererTrait;

    protected $table = 'log_referer';
    protected $fillable = [
        'url',
        'host',
        'domain_id',
        'medium',
        'source',
        'search_terms_hash',
    ];

}
