<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:32:14 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTracker_RepositoryManager_PathTrait {

    /**
     *
     * @var CTracker_Repository_Path
     */
    protected $pathRepository;

    protected function bootPathTrait() {
        $this->pathRepository = new CTracker_Repository_Path();
    }

    public function findOrCreatePath($path) {
        return $this->pathRepository->findOrCreate($path, ['path']);
    }

}
