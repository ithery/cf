<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 9:51:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Query extends CTracker_Model {

    protected $table = 'log_query';
    protected $fillable = [
        'query',
    ];

    public function arguments() {
        return $this->hasMany($this->getConfig()->get('queryArgumentModel', 'CTracker_Model_QueryArgument'));
    }

}
