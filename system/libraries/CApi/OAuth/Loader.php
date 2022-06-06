<?php
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;

class CApi_OAuth_Loader {
    /**
     * AuthorizationServer.
     *
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    private $authorizationServer;

    public function __construct(CApi_Dispatcher $dispatcher) {
        $this->registerAuthorizationServer();
    }

    /**
     * Make the authorization service instance.
     *
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    public function makeAuthorizationServer() {
        return new AuthorizationServer(
            new CApi_OAuth_Bridge_ClientRepository(new CApi_OAuth_ClientRepository()),
            new CApi_OAuth_Bridge_AccessTokenRepository(new CApi_OAuth_ClientRepository()),
            $this->app->make(Bridge\AccessTokenRepository::class),
            $this->app->make(Bridge\ScopeRepository::class),
            $this->makeCryptKey('private'),
            app('encrypter')->getKey(),
            $this->authorizationServerResponseType
        );
    }

    /**
     * Register the authorization server.
     *
     * @return void
     */
    protected function registerAuthorizationServer() {
        $this->authorizationServer = c::tap($this->makeAuthorizationServer(), function ($server) {
            $server->setDefaultScope($this->defaultScope);

            $server->enableGrantType(
                $this->makeAuthCodeGrant(),
                $this->tokensExpireIn()
            );

            $server->enableGrantType(
                $this->makeRefreshTokenGrant(),
                $this->tokensExpireIn()
            );

            $server->enableGrantType(
                $this->makePasswordGrant(),
                $this->tokensExpireIn()
            );

            $server->enableGrantType(
                new PersonalAccessGrant(),
                $this->personalAccessTokensExpireIn()
            );

            $server->enableGrantType(
                new ClientCredentialsGrant(),
                $this->tokensExpireIn()
            );

            if ($this->implicitGrantEnabled) {
                $server->enableGrantType(
                    $this->makeImplicitGrant(),
                    $this->tokensExpireIn()
                );
            }
        });
    }

    /**
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    public function getAuthorizationServer() {
        return $this->authorizationServer;
    }
}
