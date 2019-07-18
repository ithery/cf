<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:16:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CTracker_Model_Domain extends CTracker_Model {

    protected $table = 'log_domain';
    protected $fillable = [
        'name',
    ];

}
