<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 5:43:14 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTracker_RepositoryManager_GeoIpTrait {

    /**
     *
     * @var CTracker_Repository_GeoIp
     */
    protected $geoIpRepository;

    /**
     *
     * @var CTracker_GeoIp 
     */
    protected $geoIp;

    protected function bootGeoIpTrait() {
        $this->geoIpRepository = new CTracker_Repository_GeoIp();
        $this->geoIp = new CTracker_GeoIp();
    }

    public function getGeoIpId($clientIp) {
        
     
        $id = null;
        if ($geoIpData = $this->geoIp->searchAddr($clientIp)) {
            $id = $this->geoIpRepository->findOrCreate(
                    $geoIpData, ['latitude', 'longitude']
            );
        }
        return $id;
    }

}
