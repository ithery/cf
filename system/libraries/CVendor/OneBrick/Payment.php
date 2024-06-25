<?php
/**
 * @see CVendor_OneBrick
 */
class CVendor_OneBrick_Payment {
    /**
     * @var CVendor_OneBrick_Client
     */
    protected $client;

    public function __construct($baseUri, $options = []) {
        $baseUri = rtrim($baseUri, '/');
        $options['base_uri'] = $baseUri;
        $options['type'] = CVendor_OneBrick::TYPE_PAYMENT;
        $this->client = new CVendor_OneBrick_Client(new CVendor_OneBrick_Adapter_GuzzleAdapter($options), $baseUri);
    }

    public function verifyRecipientAccount($accountNumber, $bankShortCode) {
        $params = [
            'accountNumber' => $accountNumber,
            'bankShortCode' => $bankShortCode
        ];

        return $this->handleResponse($this->client->get('gs/bank-account-validation', $params));
    }

    public function getBalance() {
        return $this->handleResponse($this->client->get('gs/balance'));
    }

    public function getLedgerHistory($options = []) {
        $startDate = carr::get($options, 'startDate');
        $endDate = carr::get($options, 'endDate');
        $status = carr::get($options, 'status');
        $search = carr::get($options, 'search');
        $page = carr::get($options, 'page', 1);
        $size = carr::get($options, 'size', 10);

        $params = [
            'page' => $page,
            'size' => $size
        ];

        return $this->handleResponse($this->client->get('gs/ledger', $params));
    }

    /**
     * @param mixed $response
     *
     * @return array
     */
    public function handleResponse($response) {
        if (is_string($response)) {
            $response = json_decode($response, true);
        }
        $errCode = (int) carr::get($response, 'errCode');
        if ($errCode != 0) {
            $errMessage = carr::get($response, 'errMessage');

            throw new CVendor_Wago_Exception_ApiException($errMessage);
        }

        return carr::get($response, 'data', []);
    }
}
