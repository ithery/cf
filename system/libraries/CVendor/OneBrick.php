<?php

class CVendor_OneBrick {
    const TYPE_DATA = 'data';

    const TYPE_PAYMENT = 'payment';

    protected $options;

    public function __construct(array $options) {
        $this->options = $options;
    }

    /**
     * @param array $options
     *
     * @return CVendor_OneBrick_Data
     */
    public function data(array $options = []) {
        $baseUri = carr::get($options, 'base_uri', CF::isProduction() ? 'https://api.onebrick.io/v1' : 'https://sandbox.onebrick.io/v1');

        return new CVendor_OneBrick_Data($baseUri, $this->options);
    }

    public function payment(array $options = []) {
        $baseUri = carr::get($options, 'base_uri', CF::isProduction() ? 'https://api.onebrick.io/v2/payments' : 'https://sandbox.onebrick.io/v2/payments');

        return new CVendor_OneBrick_Payment($baseUri, $this->options);
    }
}
