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
    protected $mobileDetect;

    protected function bootDeviceTrait() {
        $this->deviceRepository = new CTracker_Repository_Device();
        $this->mobileDetect = new CTracker_Detect_MobileDetect();
    }

    public function findOrCreateDevice($data) {
        return $this->deviceRepository->findOrCreate($data, ['kind', 'model', 'platform', 'platform_version']);
    }

    public function getCurrentDeviceProperties() {
        if ($properties = $this->getDevice()) {
            $properties['platform'] = $this->getOperatingSystemFamily();
            $properties['platform_version'] = $this->getOperatingSystemVersion();
        }
        return $properties;
    }

    /**
     * @return array
     */
    private function getDevice() {

        return $this->mobileDetect->detectDevice();
    }

    /**
     * @return mixed
     */
    private function getOperatingSystemFamily() {
        try {
            return $this->userAgentParser->operatingSystem->family;
        } catch (\Exception $e) {
            return;
        }
    }

}
