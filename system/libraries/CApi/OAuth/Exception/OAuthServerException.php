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
     * @param \League\OAuth2\Server\Exception\OAuthServerException $e
     * @param \CHTTP_Response                                      $response
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
}
