<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:17:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_RepositoryManager implements CTracker_RepositoryManagerInterface {

    use CTracker_RepositoryManager_DeviceTrait;

    protected static $instance;

    /**
     *
     * @var CTracker_Repository_Session
     */
    public $sessionRepository;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CTracker_RepositoryManager();
        }
        return static::$instance;
    }

    protected function __construct() {
        $this->sessionRepository = new CTracker_Repository_Session();
        $classArray = CF::class_uses_recursive(get_class());
        foreach ($classArray as $class) {
            $methodName = 'boot' . carr::last(explode('_', $class));
            $this->$methodName();
        }
    }

    public function getSessionId($sessionData, $updateLastActivity) {
        return $this->sessionRepository->getCurrentId($sessionData, $updateLastActivity);
    }

    public function getCurrentUserId() {
        return CApp_Base::userId();
    }

}
