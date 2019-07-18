<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:16:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Referer extends CTracker_Model {

    protected $table = 'log_referer';
    protected $fillable = [
        'url',
        'host',
        'domain_id',
        'medium',
        'source',
        'search_terms_hash',
    ];

    public function domain() {
        return $this->belongsTo('CTracker_Model_Domain');
    }

}
