<?php

class CVendor_Shipper_Plugin {
    use CTrait_HasOptions;

    /**
     * @var CVendor_Shipper_Plugin_Client
     */
    protected $client;

    public function __construct($options = []) {
        $this->options = $options;
    }

    /**
     * @return \CVendor_Shipper_Plugin_Client
     */
    protected function createClient() {
        return new CVendor_Shipper_Plugin_Client($this->options);
    }

    /**
     * @return CVendor_Shipper_Plugin_Client
     */
    public function client() {
        if ($this->client == null) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }
}
