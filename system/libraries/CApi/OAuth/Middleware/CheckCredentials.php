<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

abstract class CApi_OAuth_Middleware_CheckCredentials implements CApi_Contract_ApiGroupMiddlewareInterface {
    /**
     * Api Group.
     *
     * @var string
     */
    protected $group;

    protected $request;

    public function setGroup($group) {
        $this->group = $group;

        return $this;
    }

    /**
     * @return \League\OAuth2\Server\ResourceServer
     */
    public function server() {
        return CApi::oauth($this->group)->resourceServer();
    }

    public function tokenRepository() {
        return CApi::oauth($this->group)->tokenRepository();
    }

    /**
     * Handle an incoming request.
     *
     * @param \CApi_HTTP_Request $request
     * @param \Closure           $next
     * @param mixed              ...$scopes
     *
     * @throws \CAuth_Exception_AuthenticationException
     *
     * @return mixed
     */
    public function handle(CApi_HTTP_Request $request, $next, ...$scopes) {
        $this->request = $request;
        $psr = (new PsrHttpFactory(
            new Psr17Factory(),
            new Psr17Factory(),
            new Psr17Factory(),
            new Psr17Factory()
        ))->createRequest($request);

        try {
            $psr = $this->server()->validateAuthenticatedRequest($psr);
        } catch (OAuthServerException $e) {
            throw new CAuth_Exception_AuthenticationException();
        }

        $this->validate($psr, $scopes);

        return $next($request);
    }

    /**
     * Validate the scopes and token on the incoming request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $psr
     * @param array                                    $scopes
     *
     * @throws \CApi_OAuth_Exception_MissingScopeException|\CAuth_Exception_AuthenticationException
     *
     * @return void
     */
    protected function validate($psr, $scopes) {
        $token = $this->tokenRepository()->findToken($psr->getAttribute('oauth_access_token_id'));

        $this->validateCredentials($token);

        $this->validateScopes($token, $scopes);
    }

    /**
     * Validate token credentials.
     *
     * @param null|\CApi_OAuth_Model_OAuthAccessToken $token
     *
     * @throws \CAuth_Exception_AuthenticationException
     *
     * @return void
     */
    abstract protected function validateCredentials($token);

    /**
     * Validate token scopes.
     *
     * @param null|\CApi_OAuth_Model_OAuthAccessToken $token
     * @param array                                   $scopes
     *
     * @throws \CApi_OAuth_Exception_MissingScopeException
     *
     * @return void
     */
    abstract protected function validateScopes($token, $scopes);
}
