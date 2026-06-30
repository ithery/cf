<?php

class CNotification_MessageResult {
    /**
     * @var mixed
     */
    protected $vendorResponse;

    /**
     * @param mixed $rawVendorResponse
     */
    public function __construct($rawVendorResponse) {
        $this->vendorResponse = $rawVendorResponse;
    }

    /**
     * @return mixed
     */
    public function getVendorResponse() {
        return $this->vendorResponse;
    }
}
