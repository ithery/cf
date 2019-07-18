<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 11:55:49 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Route extends CTracker_Model {

    protected $table = 'log_route';
    protected $fillable = [
        'name',
        'action',
    ];

    public function paths() {
        return $this->hasMany($this->getConfig()->get('routePathModel'));
    }

}
