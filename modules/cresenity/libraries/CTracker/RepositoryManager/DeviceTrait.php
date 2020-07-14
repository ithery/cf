<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:37:09 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTracker_RepositoryManager_DeviceTrait {

    /**
     *
     * @var CTracker_Repository_Device
     */
    protected $deviceRepository;

    protected function bootDeviceTrait() {
        $this->deviceRepository = new CTracker_Repository_Device();
    }

    public function findOrCreateDevice($data) {

        return $this->deviceRepository->findOrCreate($data, ['kind', 'model', 'platform', 'platform_version']);
    }

    public function getCurrentDeviceProperties() {
        $properties = CTracker::populator()->get('device');
        return $properties;
    }

}
