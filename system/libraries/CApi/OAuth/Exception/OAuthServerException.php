<?php

use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;

class CApi_OAuth_Exception_OAuthServerException extends Exception implements CApi_OAuth_Contract_OAuthExceptionInterface, CDebug_Contract_ShouldNotCollectException {
    /**
     * The response to render.
     *
     * @var \CHTTP_Response
     */
    protected $response;

    /**
     * Create a new OAuthServerException.
     *
     * @return void
     */
    public function __construct(LeagueException $e, CHTTP_Response $response) {
        parent::__construct($e->getMessage(), $e->getCode(), $e);

        $this->response = $response;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \CHTTP_Request $request
     *
     * @return \CHTTP_Response
     */
    public function render($request) {
        return $this->response;
    }

    /**
     * Get the HTTP response status code.
     *
     * @return int
     */
    public function statusCode() {
        return $this->response->getStatusCode();
    }

    /**
     * Report the exception.
     *
     * A 4xx comes from the client - a wrong password, an expired refresh token -
     * and is already answered by the response this exception renders, so it is
     * handled here and never reaches the logger. Returning false lets a 5xx fall
     * through and be reported as before.
     *
     * @return bool
     */
    public function report() {
        return $this->statusCode() < 500;
    }
}
