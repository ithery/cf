<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 11:53:48 PM
 */
trait CTracker_RepositoryManager_RouteTrait {
    /**
     * @var CTracker_Repository_Route
     */
    protected $routeRepository;

    protected function bootRouteTrait() {
        $this->routeRepository = new CTracker_Repository_Route();
    }

    public function pathIsTrackable($path) {
        return $this->routeRepository->pathIsTrackable($path);
    }
}
