<?php

class CVendor_BCA_Exception_RequestException extends CVendor_BCA_Exception_HttpClientException {
    /**
     * The response instance.
     *
     * @var CVendor_BCA_Response
     */
    public $response;

    /**
     * Create a new exception instance.
     *
     * @param CVendor_BCA_Response $response
     *
     * @return void
     */
    public function __construct(CVendor_BCA_Response $response) {
        parent::__construct($this->prepareMessage($response), $response->status());

        $this->response = $response;
    }

    /**
     * Prepare the exception message.
     *
     * @param CVendor_BCA_Response $response
     *
     * @return string
     */
    protected function prepareMessage(CVendor_BCA_Response $response) {
        $message = "HTTP request returned status code {$response->status()}";

        $summary = \GuzzleHttp\Psr7\Message::bodySummary($response->toPsrResponse());

        return is_null($summary) ? $message : $message .= ":\n{$summary}\n";
    }
}
