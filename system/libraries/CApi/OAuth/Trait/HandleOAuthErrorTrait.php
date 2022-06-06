<?php

trait CApi_OAuth_Trait_HandleOAuthErrorTrait {
    /**
     * Perform the given callback with exception handling.
     *
     * @param \Closure $callback
     *
     * @throws \Laravel\Passport\Exceptions\OAuthServerException
     *
     * @return mixed
     */
    protected function withErrorHandling($callback) {
        try {
            return $callback();
        } catch (LeagueException $e) {
            throw new OAuthServerException(
                $e,
                $this->convertResponse($e->generateHttpResponse(new Psr7Response()))
            );
        }
    }
}
