<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:33:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Path extends CTracker_Model {

    protected $table = 'log_path';
    protected $fillable = [
        'path',
    ];

}
