<?php

class CVendor_Xendit_Customers extends CVendor_Xendit_Base {
    /**
     * Instantiate base URL.
     *
     * @return string
     */
    protected function classUrl() {
        return '/customers';
    }

    /**
     * Send a create customer request.
     *
     * @param array $params user's parameters
     *
     * @throws Exceptions\ApiException
     *
     * @return array please check for responses parameters here
     *               https://developers.xendit.co/api-reference/?bash#create-customer
     */
    public static function createCustomer($params = []) {
        $requiredParams = ['reference_id'];

        if (array_key_exists('api-version', $params)
            && $params['api-version'] == '2020-10-31'
        ) {
            array_push(
                $requiredParams,
                'type',
                'identity_accounts',
                'kyc_documents'
            );
        } else {
            array_push($requiredParams, 'given_names');
            if (!array_key_exists('mobile_number', $params)) {
                array_push($requiredParams, 'email');
            }

            if (!array_key_exists('email', $params)) {
                array_push($requiredParams, 'mobile_number');
            }
        }

        self::validateParams($params, $requiredParams);

        $url = static::classUrl();

        return static::request('POST', $url, $params);
    }

    /**
     * Get customer by reference ID.
     *
     * @param string $reference_id reference ID
     * @param array  $params       user's parameters
     *
     * @throws CVendor_Xendit_Exception_ApiException
     *
     * @return array please check for responses parameters here
     *               https://developers.xendit.co/api-reference/?bash#get-customer-by-reference-id
     */
    public static function getCustomerByReferenceID($reference_id, $params = []) {
        $url = static::classUrl()
            . '?reference_id=' . $reference_id;

        return static::request('GET', $url, $params);
    }
}
