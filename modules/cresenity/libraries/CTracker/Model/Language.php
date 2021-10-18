<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 4:13:35 PM
 */
class CTracker_Model_Language extends CTracker_Model {
    use CModel_Tracker_TrackerLanguageTrait;

    protected $table = 'log_language';

    protected $fillable = ['preference', 'language_range'];
}
