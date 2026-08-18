<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CGeo_ProviderAbstract implements CGeo_Interface_ProviderInterface {
    /**
     * Returns the results for the 'localhost' special case.
     *
     * @return CGeo_Location
     */
    protected function getLocationForLocalhost() {
        return CGeo_Model_Address::createFromArray([
            'providedBy' => $this->getName(),
            'locality' => 'localhost',
            'country' => 'localhost',
        ]);
    }
}
