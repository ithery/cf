<?php
class CVendor_Xendit_Promotion extends CVendor_Xendit_Base {
    /**
     * Instantiate base URL
     *
     * @return string
     */
    protected function classUrl() {
        return '/promotions';
    }

    protected function createReqParams() {
        return [];
    }

    protected function updateReqParams() {
        return [];
    }

    /**
     * Send a create request
     *
     * Create a Promotion
     *
     * either promo_code or bin_list is required
     * either discount_percent or discount_amount is required
     *
     * Please refer to this documentation for more detailed info
     * https://developers.xendit.co/api-reference/#create-promotion
     *
     * @param array $params user's parameters
     *
     * @return array [
     *               'business_id' =>  string,
     *               'currency' => string,
     *               'created' => string,
     *               'description' => string,
     *               'discount_amount' => int,
     *               'end_time' => string,
     *               'id' => string,
     *               'promo_code' => string,
     *               'reference_id' => string,
     *               'start_time' => string,
     *               'status' => string,
     *               'type' => string,
     *               ]
     *
     * @throws CVendor_Xendit_Exception_InvalidArgumentException
     * @throws CVendor_Xendit_Exception_ApiException             if request status code is not 2xx
     **/
    public function create($params = []) {
        if (!array_key_exists('promo_code', $params)
            && !array_key_exists('bin_list', $params)
        ) {
            $message = 'Please specify "promo_code" or "bin_list" inside your parameters.';
            throw new InvalidArgumentException($message);
        }

        if (!array_key_exists('discount_percent', $params)
            && !array_key_exists('discount_amount', $params)
        ) {
            $message = 'Please specify "discount_percent" or "discount_amount" inside your parameters.';
            throw new InvalidArgumentException($message);
        }

        $requiredParams = [
            'reference_id',
            'description',
            'currency',
            'start_time',
            'end_time',
        ];

        $this->validateParams($params, $requiredParams);

        $url = $this->classUrl();

        return $this->request('POST', $url, $params);
    }
}
