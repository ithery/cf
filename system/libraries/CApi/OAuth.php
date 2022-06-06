<?php

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
     * @var string
     */
    private $apiGroup;

    /**
     * @var CApi_OAuth_Loader
     */
    private $loader;

    public function __construct($apiGroup) {
        $this->apiGroup = $apiGroup;
    }

    public function initLoader() {
        $this->loader = new CApi_OAuth_Loader($this->apiGroup);
    }

    /**
     * @return \League\OAuth2\Server\AuthorizationServer
     */
    public function authorizationServer() {
        return $this->loader->getAuthorizationServer();
    }

    /**
     * Enable the implicit grant type.
     *
     * @return static
     */
    public function enableImplicitGrant() {
        $this->implicitGrantEnabled = true;

        return $this;
    }

    /**
     * Set the default scope(s). Multiple scopes may be an array or specified delimited by spaces.
     *
     * @param array|string $scope
     *
     * @return void
     */
    public function setDefaultScope($scope) {
        $this->defaultScope = is_array($scope) ? implode(' ', $scope) : $scope;

        return $this;
    }

    /**
     * Get all of the defined scope IDs.
     *
     * @return array
     */
    public function scopeIds() {
        return $this->scopes()->pluck('id')->values()->all();
    }

    /**
     * Determine if the given scope has been defined.
     *
     * @param string $id
     *
     * @return bool
     */
    public function hasScope($id) {
        return $id === '*' || array_key_exists($id, $this->scopes);
    }

    /**
     * Get all of the scopes defined for the application.
     *
     * @return \CCollection
     */
    public function scopes() {
        return c::collect($this->scopes)->map(function ($description, $id) {
            return new CApi_OAuth_Scope($id, $description);
        })->values();
    }

    /**
     * Get all of the scopes matching the given IDs.
     *
     * @param array $ids
     *
     * @return array
     */
    public function scopesFor(array $ids) {
        return c::collect($ids)->map(function ($id) {
            if (isset($this->scopes[$id])) {
                return new CApi_OAuth_Scope($id, $this->scopes[$id]);
            }
        })->filter()->values()->all();
    }

    /**
     * Define the scopes for the application.
     *
     * @param array $scopes
     *
     * @return void
     */
    public static function tokensCan(array $scopes) {
        static::$scopes = $scopes;
    }

    /**
     * Get or set when access tokens expire.
     *
     * @param null|\DateTimeInterface $date
     *
     * @return \DateInterval|static
     */
    public function tokensExpireIn(DateTimeInterface $date = null) {
        if (is_null($date)) {
            return $this->tokensExpireAt
                            ? CCarbon::now()->diff($this->tokensExpireAt)
                            : new DateInterval('P1Y');
        }

        $this->tokensExpireAt = $date;

        return $this;
    }

    /**
     * Get or set when refresh tokens expire.
     *
     * @param null|\DateTimeInterface $date
     *
     * @return \DateInterval|static
     */
    public function refreshTokensExpireIn(DateTimeInterface $date = null) {
        if (is_null($date)) {
            return $this->refreshTokensExpireAt
                            ? CCarbon::now()->diff($this->refreshTokensExpireAt)
                            : new DateInterval('P1Y');
        }

        $this->refreshTokensExpireAt = $date;

        return $this;
    }

    /**
     * Get or set when personal access tokens expire.
     *
     * @param null|\DateTimeInterface $date
     *
     * @return \DateInterval|static
     */
    public function personalAccessTokensExpireIn(DateTimeInterface $date = null) {
        if (is_null($date)) {
            return $this->personalAccessTokensExpireAt
                ? CCarbon::now()->diff($this->personalAccessTokensExpireAt)
                : new DateInterval('P1Y');
        }

        $this->personalAccessTokensExpireAt = $date;

        return $this;
    }

    /**
     * Get or set the name for API token cookies.
     *
     * @param null|string $cookie
     *
     * @return string|static
     */
    public function cookie($cookie = null) {
        if (is_null($cookie)) {
            return $this->cookie;
        }

        $this->cookie = $cookie;

        return $this;
    }

    /**
     * Indicate that Passport should ignore incoming CSRF tokens.
     *
     * @param bool $ignoreCsrfToken
     *
     * @return static
     */
    public function ignoreCsrfToken($ignoreCsrfToken = true) {
        $this->ignoreCsrfToken = $ignoreCsrfToken;

        return $this;
    }

    /**
     * Set the current user for the application with the given scopes.
     *
     * @param \CAuth_AuthenticatableInterface|\CApi_OAuth_Trait_HasApiTokenTrait $user
     * @param array                                                              $scopes
     * @param string                                                             $guard
     *
     * @return \CAuth_AuthenticatableInterface
     */
    public static function actingAs($user, $scopes = [], $guard = 'api') {
        $token = Mockery::mock(self::tokenModel())->shouldIgnoreMissing(false);

        foreach ($scopes as $scope) {
            $token->shouldReceive('can')->with($scope)->andReturn(true);
        }

        $user->withAccessToken($token);

        if (isset($user->wasRecentlyCreated) && $user->wasRecentlyCreated) {
            $user->wasRecentlyCreated = false;
        }

        c::auth()->guard($guard)->setUser($user);

        c::auth()->shouldUse($guard);

        return $user;
    }

    /**
     * Set the current client for the application with the given scopes.
     *
     * @param \Laravel\Passport\Client $client
     * @param array                    $scopes
     *
     * @return \Laravel\Passport\Client
     */
    public static function actingAsClient($client, $scopes = []) {
        $token = app(self::tokenModel());

        $token->client_id = $client->id;
        $token->setRelation('client', $client);

        $token->scopes = $scopes;

        $mock = Mockery::mock(ResourceServer::class);
        $mock->shouldReceive('validateAuthenticatedRequest')
            ->andReturnUsing(function (ServerRequestInterface $request) use ($token) {
                return $request->withAttribute('oauth_client_id', $token->client->id)
                    ->withAttribute('oauth_access_token_id', $token->id)
                    ->withAttribute('oauth_scopes', $token->scopes);
            });

        app()->instance(ResourceServer::class, $mock);

        $mock = Mockery::mock(TokenRepository::class);
        $mock->shouldReceive('find')->andReturn($token);

        app()->instance(TokenRepository::class, $mock);

        return $client;
    }

    /**
     * Set the storage location of the encryption keys.
     *
     * @param string $path
     *
     * @return void
     */
    public function loadKeysFrom($path) {
        $this->keyPath = $path;
    }

    /**
     * The location of the encryption keys.
     *
     * @param string $file
     *
     * @return string
     */
    public function keyPath($file) {
        $file = ltrim($file, '/\\');

        return $this->keyPath
            ? rtrim($this->keyPath, '/\\') . DIRECTORY_SEPARATOR . $file
            : DOCROOT . $file;
    }

    /**
     * Set the auth code model class name.
     *
     * @param string $authCodeModel
     *
     * @return void
     */
    public function useAuthCodeModel($authCodeModel) {
        $this->authCodeModel = $authCodeModel;
    }

    /**
     * Get the auth code model class name.
     *
     * @return string
     */
    public static function authCodeModel() {
        return static::$authCodeModel;
    }

    /**
     * Get a new auth code model instance.
     *
     * @return \Laravel\Passport\AuthCode
     */
    public static function authCode() {
        return new static::$authCodeModel();
    }

    /**
     * Set the client model class name.
     *
     * @param string $clientModel
     *
     * @return void
     */
    public static function useClientModel($clientModel) {
        static::$clientModel = $clientModel;
    }

    /**
     * Get the client model class name.
     *
     * @return string
     */
    public static function clientModel() {
        return static::$clientModel;
    }

    /**
     * Get a new client model instance.
     *
     * @return \Laravel\Passport\Client
     */
    public static function client() {
        return new static::$clientModel();
    }

    /**
     * Determine if clients are identified using UUIDs.
     *
     * @return bool
     */
    public static function clientUuids() {
        return static::$clientUuids;
    }

    /**
     * Specify if clients are identified using UUIDs.
     *
     * @param bool $value
     *
     * @return void
     */
    public static function setClientUuids($value) {
        static::$clientUuids = $value;
    }

    /**
     * Set the personal access client model class name.
     *
     * @param string $clientModel
     *
     * @return void
     */
    public static function usePersonalAccessClientModel($clientModel) {
        static::$personalAccessClientModel = $clientModel;
    }

    /**
     * Get the personal access client model class name.
     *
     * @return string
     */
    public static function personalAccessClientModel() {
        return static::$personalAccessClientModel;
    }

    /**
     * Get a new personal access client model instance.
     *
     * @return \Laravel\Passport\PersonalAccessClient
     */
    public static function personalAccessClient() {
        return new static::$personalAccessClientModel();
    }

    /**
     * Set the token model class name.
     *
     * @param string $tokenModel
     *
     * @return void
     */
    public static function useTokenModel($tokenModel) {
        static::$tokenModel = $tokenModel;
    }

    /**
     * Get the token model class name.
     *
     * @return string
     */
    public static function tokenModel() {
        return static::$tokenModel;
    }

    /**
     * Get a new personal access client model instance.
     *
     * @return \Laravel\Passport\Token
     */
    public static function token() {
        return new static::$tokenModel();
    }

    /**
     * Set the refresh token model class name.
     *
     * @param string $refreshTokenModel
     *
     * @return void
     */
    public static function useRefreshTokenModel($refreshTokenModel) {
        static::$refreshTokenModel = $refreshTokenModel;
    }

    /**
     * Get the refresh token model class name.
     *
     * @return string
     */
    public static function refreshTokenModel() {
        return static::$refreshTokenModel;
    }

    /**
     * Get a new refresh token model instance.
     *
     * @return \CApi_OAuth_Model_OAuthRefreshToken
     */
    public function refreshToken() {
        $refreshTokenModel = $this->refreshTokenModel;

        return new $refreshTokenModel();
    }

    /**
     * Configure Passport to hash client credential secrets.
     *
     * @return static
     */
    public function hashClientSecrets() {
        $this->hashesClientSecrets = true;

        return $this;
    }

    /**
     * Instruct Passport to enable cookie serialization.
     *
     * @return static
     */
    public function withCookieSerialization() {
        $this->unserializesCookies = true;

        return $this;
    }

    /**
     * Instruct Passport to disable cookie serialization.
     *
     * @return static
     */
    public function withoutCookieSerialization() {
        $this->unserializesCookies = false;

        return $this;
    }
}
