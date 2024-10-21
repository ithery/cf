<?php

use DigitalOceanV2\DigitalOceanV2;
use DigitalOceanV2\Adapter\GuzzleHttpAdapter;

class CVendor_DigitalOcean {
    /**
     * @var \DigitalOceanV2\DigitalOceanV2
     */
    protected $do;

    public function __construct($accessToken) {
        // create an adapter with your access token which can be
        // generated at https://cloud.digitalocean.com/settings/applications
        $adapter = new GuzzleHttpAdapter($accessToken);

        // create a digital ocean object with the previous adapter
        $this->do = new DigitalOceanV2($adapter);
    }

    /**
     * @return \DigitalOceanV2\DigitalOceanV2
     */
    public function getObject() {
        return $this->do;
    }
}
