<?php

abstract class CVendor_Xendit_Base {
    protected $apiRequestor;

    public function __construct(CVendor_Xendit_ApiRequestor $apiRequestor) {
        $this->apiRequestor = $apiRequestor;
    }

    /**
     * Instantiate base URL.
     *
     * @return string
     */
    abstract protected function classUrl();

    /**
     * Parameters validation.
     *
     * @param array $params         user's parameters
     * @param array $requiredParams required parameters
     *
     * @throws CVendor_Xendit_Exception_InvalidArgumentException
     *
     * @return void
     */
    protected function validateParams($params = [], $requiredParams = []) {
        $currParams = array_diff_key(array_flip($requiredParams), $params);
        if ($params && !is_array($params)) {
            $message = 'You must pass an array as params.';

            throw new CVendor_Xendit_Exception_InvalidArgumentException($message);
        }
        if (count($currParams) > 0) {
            $message = 'You must pass required parameters on your params. '
            . 'Check https://xendit.github.io/apireference/ for more information.';

            throw new CVendor_Xendit_Exception_InvalidArgumentException($message);
        }
    }

    /**
     * Send request to Api Requestor.
     *
     * @param $method string
     * @param $url    string ext url to the API
     * @param $params array parameters
     *
     * @throws CVendor_Xendit_Exception_ApiException
     *
     * @return array
     */
    protected function request(
        $method,
        $url,
        $params = []
    ) {
        $headers = [];

        if (array_key_exists('for-user-id', $params)) {
            $headers['for-user-id'] = $params['for-user-id'];
        }

        if (array_key_exists('with-fee-rule', $params)) {
            $headers['with-fee-rule'] = $params['with-fee-rule'];
        }

        if (array_key_exists('X-IDEMPOTENCY-KEY', $params)) {
            $headers['X-IDEMPOTENCY-KEY'] = $params['X-IDEMPOTENCY-KEY'];
        }

        if (array_key_exists('api-version', $params)) {
            $headers['api-version'] = $params['api-version'];
        }

        if (array_key_exists('X-API-VERSION', $params)) {
            $headers['X-API-VERSION'] = $params['X-API-VERSION'];
        }

        $requestor = $this->apiRequestor;

        return $requestor->request($method, $url, $params, $headers);
    }
}
