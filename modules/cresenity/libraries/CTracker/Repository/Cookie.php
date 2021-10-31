<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 3:29:11 PM
 */
use Ramsey\Uuid\Uuid as UUID;

class CTracker_Repository_Cookie extends CTracker_AbstractRepository {
    /**
     * @var CCookie_Jar
     */
    private $cookieJar;

    public function __construct() {
        $this->className = CTracker::config()->get('cookieModel', CTracker_Model_Cookie::class);
        $this->createModel();
        $this->config = CTracker::config();
        parent::__construct();
    }

    public function getId() {
        if (!$this->config->isLogCookie()) {
            return;
        }
        $cookieUuid = CTracker::populator()->get('cookie.uuid');

        return $this->findOrCreate(['uuid' => $cookieUuid]);
    }
}
