<?php
class CHTTP_Client_Exception_RequestException extends CHTTP_Client_Exception {
    /**
     * The response instance.
     *
     * @var \CHTTP_Client_Response
     */
    public $response;

    /**
     * Create a new exception instance.
     *
     * @param \CHTTP_Client_Response $response
     *
     * @return void
     */
    public function __construct(CHTTP_Client_Response $response) {
        parent::__construct($this->prepareMessage($response), $response->status());

        $this->response = $response;
    }

    /**
     * Prepare the exception message.
     *
     * @param \CHTTP_Client_Response $response
     *
     * @return string
     */
    protected function prepareMessage(CHTTP_Client_Response $response) {
        $message = "HTTP request returned status code {$response->status()}";

        $summary = GuzzleHttp\Psr7\Message::bodySummary($response->toPsrResponse());

        return is_null($summary) ? $message : $message .= ":\n{$summary}\n";
    }
}
