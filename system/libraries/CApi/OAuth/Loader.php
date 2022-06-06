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

    private $apiGroup;

    private $bridgeRefreshTokenRepository;

    public function __construct($apiGroup) {
        $this->apiGroup = $apiGroup;
        $this->registerAuthorizationServer();
        // $this->registerClientRepository();
        // $this->registerJWTParser();
        // $this->registerResourceServer();
        // $this->registerGuard();
    }

    /**
     * Make the authorization service instance.
     *
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    public function makeAuthorizationServer() {
        return new AuthorizationServer(
            new CApi_OAuth_Bridge_ClientRepository(new CApi_OAuth_ClientRepository()),
            new CApi_OAuth_Bridge_AccessTokenRepository(new CApi_OAuth_TokenRepository()),
            new CApi_OAuth_Bridge_ScopeRepository(new CApi_OAuth_TokenRepository()),
            $this->makeCryptKey('private'),
            CCrypt::encrypter()->getKey(),
            CApi::oauth()->authorizationServerResponseType
        );
    }

    /**
     * Register the authorization server.
     *
     * @return void
     */
    protected function registerAuthorizationServer() {
        $this->authorizationServer = c::tap($this->makeAuthorizationServer(), function ($server) {
            $server->setDefaultScope(CApi::oauth()->defaultScope);

            $server->enableGrantType(
                $this->makeAuthCodeGrant(),
                CApi::oauth()->tokensExpireIn()
            );

            $server->enableGrantType(
                $this->makeRefreshTokenGrant(),
                CApi::oauth()->tokensExpireIn()
            );

            $server->enableGrantType(
                $this->makePasswordGrant(),
                CApi::oauth()->tokensExpireIn()
            );

            $server->enableGrantType(
                new CApi_OAuth_Bridge_PersonalAccessGrant(),
                CApi::oauth()->personalAccessTokensExpireIn()
            );

            $server->enableGrantType(
                new ClientCredentialsGrant(),
                CApi::oauth()->tokensExpireIn()
            );

            if (CApi::oauth()->implicitGrantEnabled) {
                $server->enableGrantType(
                    $this->makeImplicitGrant(),
                    CApi::oauth()->tokensExpireIn()
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

    /**
     * Create and configure an instance of the Auth Code grant.
     *
     * @return \League\OAuth2\Server\Grant\AuthCodeGrant
     */
    protected function makeAuthCodeGrant() {
        return c::tap($this->buildAuthCodeGrant(), function ($grant) {
            $grant->setRefreshTokenTTL(CApi::oauth()->refreshTokensExpireIn());
        });
    }

    /**
     * Build the Auth Code grant instance.
     *
     * @return \League\OAuth2\Server\Grant\AuthCodeGrant
     */
    protected function buildAuthCodeGrant() {
        return new AuthCodeGrant(
            new CApi_OAuth_Bridge_AuthCodeRepository(),
            $this->getBridgeRefreshTokenRepository(),
            new DateInterval('PT10M')
        );
    }

    /**
     * Create and configure a Refresh Token grant instance.
     *
     * @return \League\OAuth2\Server\Grant\RefreshTokenGrant
     */
    protected function makeRefreshTokenGrant() {
        $repository = $this->getBridgeRefreshTokenRepository();

        return c::tap(new RefreshTokenGrant($repository), function ($grant) {
            $grant->setRefreshTokenTTL(CApi::oauth()->refreshTokensExpireIn());
        });
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return \League\OAuth2\Server\Grant\PasswordGrant
     */
    protected function makePasswordGrant() {
        $grant = new PasswordGrant(
            new CApi_OAuth_Bridge_UserRepository(),
            $this->getBridgeRefreshTokenRepository(),
        );

        $grant->setRefreshTokenTTL(CApi::oauth()->refreshTokensExpireIn());

        return $grant;
    }

    /**
     * Create and configure an instance of the Implicit grant.
     *
     * @return \League\OAuth2\Server\Grant\ImplicitGrant
     */
    protected function makeImplicitGrant() {
        return new ImplicitGrant(CApi::oauth()->tokensExpireIn());
    }

    /**
     * Register the client repository.
     *
     * @return void
     */
    protected function registerClientRepository() {
        $this->app->singleton(ClientRepository::class, function ($container) {
            $config = $container->make('config')->get('passport.personal_access_client');

            return new ClientRepository($config['id'] ?? null, $config['secret'] ?? null);
        });
    }

    /**
     * Register the JWT Parser.
     *
     * @return void
     */
    protected function registerJWTParser() {
        $this->app->singleton(Parser::class, function () {
            return Configuration::forUnsecuredSigner()->parser();
        });
    }

    /**
     * Register the resource server.
     *
     * @return void
     */
    protected function registerResourceServer() {
        $this->app->singleton(ResourceServer::class, function ($container) {
            return new ResourceServer(
                $container->make(Bridge\AccessTokenRepository::class),
                $this->makeCryptKey('public')
            );
        });
    }

    /**
     * Create a CryptKey instance without permissions check.
     *
     * @param string $type
     *
     * @return \League\OAuth2\Server\CryptKey
     */
    protected function makeCryptKey($type) {
        $key = str_replace('\\n', "\n", CF::config('api.groups.' . $this->apiGroup . '.oauth.' . $type . '_key') ?: '');

        if (!$key) {
            $key = 'file://' . CApi::oauth()->keyPath('oauth-' . $type . '.key');
        }

        return new CryptKey($key, null, false);
    }

    /**
     * Register the token guard.
     *
     * @return void
     */
    protected function registerGuard() {
        CAuth::manager()->extend('oauth', function ($app, $name, array $config) {
            return c::tap($this->makeGuard($config), function ($guard) {
                $guard->setRequest(c::request());
            });
        });
    }

    /**
     * Make an instance of the token guard.
     *
     * @param array $config
     *
     * @return \CAuth_Guard_RequestGuard
     */
    protected function makeGuard(array $config) {
        return new CAuth_Guard_RequestGuard(function ($request) use ($config) {
            return (new CApi_OAuth_Guard_TokenGuard(
                $this->app->make(ResourceServer::class),
                new PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
                $this->app->make(TokenRepository::class),
                $this->app->make(ClientRepository::class),
                $this->app->make('encrypter')
            ))->user($request);
        }, $this->app['request']);
    }

    /**
     * Register the cookie deletion event handler.
     *
     * @return void
     */
    protected function deleteCookieOnLogout() {
        Event::listen(Logout::class, function () {
            if (Request::hasCookie(CApi::oauth()->cookie())) {
                Cookie::queue(Cookie::forget(CApi::oauth()->cookie()));
            }
        });
    }

    protected function getBridgeRefreshTokenRepository() {
        if ($this->bridgeRefreshTokenRepository == null) {
            $this->bridgeRefreshTokenRepository = new CApi_OAuth_Bridge_RefreshTokenRepository(new CApi_OAuth_RefreshTokenRepository());
        }

        return $this->bridgeRefreshTokenRepository;
    }
}
