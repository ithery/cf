<?php

class CVendor_Xendit_QRCode extends CVendor_Xendit_Base {
    /**
     * Instantiate base URL
     *
     * @return string
     */
    protected function classUrl() {
        return '/qr_codes';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    protected function createReqParams() {
        return [];
    }

    /**
     * Instantiate required params for Update
     *
     * @return array
     */
    protected function updateReqParams() {
        return [];
    }

    /**
     * Send a create request
     *
     * Create a QR Code
     * Required parameters: external_id, type, callback_url, amount.
     * For DYNAMIC QR Code type, amount is required.
     * For STATIC QR Code type, amount will be ignored.
     *
     * To create QR Code for a Xenplatform sub-account,
     * include for-user-id in $params
     *
     * Please refer to this documentation for more detailed info
     * https://xendit.github.io/apireference/#create-qr-code
     *
     * @param array $params user's parameters
     *
     * @return array [
     *               'id' =>  string,
     *               'external_id' => string,
     *               'amount' => int,
     *               'qr_string' => string,
     *               'callback_url' => string,
     *               'type' => 'DYNAMIC' || 'STATIC',
     *               'status' => 'ACTIVE' || 'INACTIVE',
     *               'created' => date,
     *               'updated' => date,
     *               ]
     *
     * @throws CVendor_Xendit_Exception_InvalidArgumentException if type is not exist or not one of DYNAMIC or STATIC
     * @throws CVendor_Xendit_Exception_ApiException             if request status code is not 2xx
     **/
    public function create($params = []) {
        $requiredParams = [];

        if (!array_key_exists('type', $params)) {
            $message = 'Please specify "type" inside your parameters.';
            throw new InvalidArgumentException($message);
        }

        if ($params['type'] === 'DYNAMIC') {
            $requiredParams = ['external_id', 'type', 'callback_url', 'amount'];
        } elseif ($params['type'] === 'STATIC') {
            $requiredParams = ['external_id', 'type', 'callback_url'];
        } else {
            $message = 'Invalid QR Code type';
            throw new InvalidArgumentException($message);
        }

        $this->validateParams($params, $requiredParams);

        $url = $this->classUrl();

        return $this->request('POST', $url, $params);
    }

    /**
     * Get QR Code
     *
     * Get a QR Code by its external_id
     *
     * Please refer to this documentation for more detailed info
     * https://xendit.github.io/apireference/#get-qr-code-by-external-id
     *
     * @param string $external_id Merchant provided unique ID used to create QR code
     *
     * @return array [
     *               'id' =>  string,
     *               'external_id' => string,
     *               'amount' => int,
     *               'qr_string' => string,
     *               'callback_url' => string,
     *               'type' => 'DYNAMIC' || 'STATIC',
     *               'status' => 'ACTIVE' || 'INACTIVE',
     *               'created' => date,
     *               'updated' => date,
     *               ]
     *
     * @throws CVendor_Xendit_Exception_ApiException
     **/
    public function get(string $external_id) {
        $url = $this->classUrl() . '/' . $external_id;

        return $this->request('GET', $url);
    }
}
