<?php
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;

class CApi_OAuth {
    /**
     * Indicates if the implicit grant type is enabled.
     *
     * @var null|bool
     */
    public $implicitGrantEnabled = false;

    /**
     * The default scope.
     *
     * @var string
     */
    public $defaultScope;

    /**
     * All of the scopes defined for the application.
     *
     * @var array
     */
    public $scopes = [

    ];

    /**
     * The date when access tokens expire.
     *
     * @var null|\DateTimeInterface
     *
     * @deprecated will be removed in the next major Passport release
     */
    public $tokensExpireAt;

    /**
     * The interval when access tokens expire.
     *
     * @var null|\DateInterval
     */
    public $tokensExpireIn;

    /**
     * The date when refresh tokens expire.
     *
     * @var null|\DateTimeInterface
     *
     * @deprecated will be removed in the next major Passport release
     */
    public $refreshTokensExpireAt;

    /**
     * The date when refresh tokens expire.
     *
     * @var null|\DateInterval
     */
    public $refreshTokensExpireIn;

    /**
     * The date when personal access tokens expire.
     *
     * @var null|\DateTimeInterface
     *
     * @deprecated will be removed in the next major Passport release
     */
    public $personalAccessTokensExpireAt;

    /**
     * The date when personal access tokens expire.
     *
     * @var null|\DateInterval
     */
    public $personalAccessTokensExpireIn;

    /**
     * The name for API token cookies.
     *
     * @var string
     */
    public $cookie = 'laravel_token';

    /**
     * Indicates if Passport should ignore incoming CSRF tokens.
     *
     * @var bool
     */
    public $ignoreCsrfToken = false;

    /**
     * The storage location of the encryption keys.
     *
     * @var string
     */
    public $keyPath;

    /**
     * The auth code model class name.
     *
     * @var string
     */
    public $authCodeModel = 'Laravel\Passport\AuthCode';

    /**
     * The client model class name.
     *
     * @var string
     */
    public $clientModel = 'Laravel\Passport\Client';

    /**
     * Indicates if client's are identified by UUIDs.
     *
     * @var bool
     */
    public $clientUuids = false;

    /**
     * The personal access client model class name.
     *
     * @var string
     */
    public $personalAccessClientModel = 'Laravel\Passport\PersonalAccessClient';

    /**
     * The token model class name.
     *
     * @var string
     */
    public $tokenModel = 'Laravel\Passport\Token';

    /**
     * The refresh token model class name.
     *
     * @var string
     */
    public $refreshTokenModel = 'Laravel\Passport\RefreshToken';

    /**
     * Indicates if Passport migrations will be run.
     *
     * @var bool
     */
    public $runsMigrations = true;

    /**
     * Indicates if Passport should unserializes cookies.
     *
     * @var bool
     */
    public $unserializesCookies = false;

    /**
     * Indicates if client secrets will be hashed.
     *
     * @var bool
     */
    public $hashesClientSecrets = false;

    /**
     * The callback that should be used to generate JWT encryption keys.
     *
     * @var callable
     */
    public $tokenEncryptionKeyCallback;

    /**
     * Indicates the scope should inherit its parent scope.
     *
     * @var bool
     */
    public $withInheritedScopes = false;

    /**
     * The authorization server response type.
     *
     * @var null|\League\OAuth2\Server\ResponseTypes\ResponseTypeInterface
     */
    public $authorizationServerResponseType;

    /**
     * AuthorizationServer.
     *
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    private $authorizationServer;

    public function __construct() {
        $this->registerAuthorizationServer();
    }

    /**
     * Make the authorization service instance.
     *
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    public function makeAuthorizationServer() {
        return new AuthorizationServer(
            new CApi_OAuth_Bridge_ClientRepository(),
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
}
