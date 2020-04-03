<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:17:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_RepositoryManager implements CTracker_RepositoryManagerInterface {

    use CTracker_RepositoryManager_DeviceTrait,
        CTracker_RepositoryManager_GeoIpTrait,
        CTracker_RepositoryManager_AgentTrait,
        CTracker_RepositoryManager_RefererTrait,
        CTracker_RepositoryManager_CookieTrait,
        CTracker_RepositoryManager_DomainTrait,
        CTracker_RepositoryManager_LanguageTrait,
        CTracker_RepositoryManager_SessionTrait,
        CTracker_RepositoryManager_PathTrait,
        CTracker_RepositoryManager_QueryTrait,
        CTracker_RepositoryManager_SqlQueryTrait,
        CTracker_RepositoryManager_RouteTrait,
        CTracker_RepositoryManager_LogTrait;

    protected static $instance;

    /**
     *
     * @var CTracker_Parser_UserAgentParser 
     */
    protected $userAgentParser;

    /**
     *
     * @var CTracker_Detect_CrawlerDetect
     */
    protected $crawlerDetector;

    /**
     * 
     * @return CTracker_RepositoryManager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CTracker_RepositoryManager();
        }
        return static::$instance;
    }

    protected function __construct() {
        $this->userAgentParser = new CTracker_Parser_UserAgentParser(DOCROOT);
        $this->crawlerDetector = new CTracker_Detect_CrawlerDetect(CTracker::populator()->get('request.headers'), CTracker::populator()->get('request.userAgent'));
        $classArray = CF::class_uses_recursive(get_class());
        foreach ($classArray as $class) {
            $methodName = 'boot' . carr::last(explode('_', $class));
            $this->$methodName();
        }
    }

    public function getCurrentUserId() {
        return CTracker::populator()->get('user.userId');
    }

    public function isRobot() {
        return $this->crawlerDetector->isRobot();
    }

    public function parserIsAvailable() {
        return !empty($this->userAgentParser);
    }

    public function pageViews(CPeriod $minutes, $results) {
        return $this->logRepository->pageViews($minutes, $results);
    }

    public function pageViewsByCountry(CPeriod $minutes, $results) {
        return $this->logRepository->pageViewsByCountry($minutes, $results);
    }

}
