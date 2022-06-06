<?php

use Nyholm\Psr7\Response as Psr7Response;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;

trait CApi_OAuth_Trait_HandleOAuthErrorTrait {
    use CApi_OAuth_Trait_ConvertPsrResponseTrait;

    /**
     * Perform the given callback with exception handling.
     *
     * @param \Closure $callback
     *
     * @throws \CApi_OAuth_Exception_OAuthServerException
     *
     * @return mixed
     */
    protected function withErrorHandling($callback) {
        try {
            return $callback();
        } catch (LeagueException $e) {
            throw new CApi_OAuth_Exception_OAuthServerException(
                $e,
                $this->convertResponse($e->generateHttpResponse(new Psr7Response()))
            );
        }
    }
}
