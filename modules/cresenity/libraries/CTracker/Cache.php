<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:33:21 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Cache extends CCache_Repository {

    public function __construct($options = array()) {
        if (empty($options)) {
            $options = array(
                'driver' => 'File',
                'options' => array(
                    'engine' => 'Temp',
                    'options' => array(
                        'directory' => 'CTracker'
                    ),
                ),
            );
        }
        parent::__construct($options);
    }

}
