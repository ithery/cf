<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:21:11 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTracker_RepositoryManager_SessionTrait {

    /**
     *
     * @var CTracker_Repository_Session
     */
    public $sessionRepository;

    protected function bootSessionTrait() {
        $this->sessionRepository = new CTracker_Repository_Session();
    }

    public function getSessionId($sessionData, $updateLastActivity) {

        return $this->sessionRepository->getCurrentId($sessionData, $updateLastActivity);
    }

    public function checkSessionData($newData, $currentData) {
        if ($newData && $currentData && $newData !== $currentData) {
            $newData = $this->updateSessionData($newData);
        }
        return $newData;
    }

    public function getLastSessions($minutes, $results) {
        return $this->sessionRepository->last($minutes, $results);
    }

}
