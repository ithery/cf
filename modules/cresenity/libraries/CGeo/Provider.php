<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:04:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CGeo_Provider implements CGeo_Interface_ProviderInterface {

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
