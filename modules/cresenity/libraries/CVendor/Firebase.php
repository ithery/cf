<?php

use Psr\Log\LogLevel;
use GuzzleHttp\Client;
use Beste\Clock\SystemClock;
use GuzzleHttp\HandlerStack;
use Psr\Log\LoggerInterface;
use Psr\Clock\ClockInterface;
use Google\Auth\CredentialsLoader;
use Google\Auth\SignBlobInterface;
use Psr\Http\Message\UriInterface;
use Psr\Cache\CacheItemPoolInterface;
use Google\Cloud\Storage\StorageClient;
use GuzzleHttp\Psr7\Utils as GuzzleUtils;
use Google\Auth\Cache\MemoryCacheItemPool;
use Google\Auth\Credentials\GCECredentials;
use Google\Auth\ProjectIdProviderInterface;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Auth\Middleware\AuthTokenMiddleware;
use Google\Auth\Credentials\ServiceAccountCredentials;

class CVendor_Firebase {
    const API_CLIENT_SCOPES = [
        'https://www.googleapis.com/auth/iam',
        'https://www.googleapis.com/auth/cloud-platform',
        'https://www.googleapis.com/auth/firebase',
        'https://www.googleapis.com/auth/firebase.database',
        'https://www.googleapis.com/auth/firebase.messaging',
        'https://www.googleapis.com/auth/firebase.remoteconfig',
        'https://www.googleapis.com/auth/userinfo.email',
    ];

    /**
     * @var null|ServiceAccount
     */
    protected $serviceAccount;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $httpClientConfig = [];

    /**
     * @var array
     */
    protected $httpClientMiddlewares = [];

    /**
     * @var bool
     */
    private $discoveryIsDisabled = false;

    /**
     * @var null|string
     */
    private $projectId = null;

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @var null|string
     */
    private $clientEmail = null;

    /**
     * @var CacheItemPoolInterface
     */
    private $verifierCache;

    /**
     * @var CacheItemPoolInterface
     */
    private $authTokenCache;

    /**
     * @var null|string
     */
    private $databaseUri = null;

    /**
     * @var null|string
     */
    private $defaultStorageBucket = null;

    /**
     * @var string
     */
    private static $databaseUriPattern = 'https://%s.firebaseio.com';

    /**
     * @var string
     */
    private static $storageBucketNamePattern = '%s.appspot.com';

    /**
     * @var ClockInterface
     */
    private $clock;

    public function __construct($config = []) {
        $this->clock = SystemClock::create();
        $this->config = $config;
        $this->verifierCache = new MemoryCacheItemPool();
        $this->authTokenCache = new MemoryCacheItemPool();
    }

    protected function getServiceAccount() {
        $config = $this->config;

        if (!$this->serviceAccount) {
            if (strlen(carr::get($config, 'json_credentials')) > 0) {
                $this->serviceAccount = CVendor_Firebase_ServiceAccount::fromValue(carr::get($config, 'json_credentials'));
            } else {
                if (is_array($config)) {
                    $this->serviceAccount = CVendor_Firebase_ServiceAccount::fromArray($config);
                }
            }
        }

        if (!$this->serviceAccount) {
            throw new CVendor_Firebase_Exception_LogicException('No service account has been configured.');
        }

        return $this->serviceAccount;
    }

    /**
     * @return string
     */
    private function getProjectId() {
        if ($this->projectId !== null) {
            return $this->projectId;
        }

        $serviceAccount = $this->getServiceAccount();

        if ($serviceAccount !== null) {
            return $this->projectId = $serviceAccount->getProjectId();
        }

        if ($this->discoveryIsDisabled) {
            throw new RuntimeException('Unable to determine the Firebase Project ID, and credential discovery is disabled');
        }

        if (($credentials = $this->getGoogleAuthTokenCredentials())
            && ($credentials instanceof ProjectIdProviderInterface)
            && ($projectId = $credentials->getProjectId())
        ) {
            return $this->projectId = $projectId;
        }

        if ($projectId = CVendor_Firebase_Util::getenv('GOOGLE_CLOUD_PROJECT')) {
            return $this->projectId = $projectId;
        }

        throw new RuntimeException('Unable to determine the Firebase Project ID');
    }

    private function getClientEmail(): ?string {
        if ($this->clientEmail !== null) {
            return $this->clientEmail;
        }

        $serviceAccount = $this->getServiceAccount();

        if ($serviceAccount !== null) {
            return $this->clientEmail = (string) (new CVendor_Firebase_Value_Email($serviceAccount->getClientEmail()));
        }

        if ($this->discoveryIsDisabled) {
            return null;
        }

        try {
            if (($credentials = $this->getGoogleAuthTokenCredentials())
                && ($credentials instanceof SignBlobInterface)
                && ($clientEmail = $credentials->getClientName())
            ) {
                return $this->clientEmail = $clientEmail;
            }
        } catch (Throwable $e) {
            return null;
        }

        return null;
    }

    private function getDatabaseUri(): UriInterface {
        if ($this->databaseUri === null) {
            $this->databaseUri = \sprintf(self::$databaseUriPattern, $this->getProjectId());
        }

        return GuzzleUtils::uriFor($this->databaseUri);
    }

    private function getStorageBucketName(): string {
        if ($this->defaultStorageBucket === null) {
            $this->defaultStorageBucket = \sprintf(self::$storageBucketNamePattern, $this->getProjectId());
        }

        return $this->defaultStorageBucket;
    }

    /**
     * @return CVendor_Firebase_AuthInterface
     */
    public function createAuth() {
        $projectId = $this->getProjectId();
        $tenantId = $this->tenantId;

        $httpClient = $this->createApiClient([
            'base_uri' => 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/',
        ]);

        $authApiClient = new CVendor_Firebase_Auth_ApiClient($httpClient, $tenantId);
        $customTokenGenerator = $this->createCustomTokenGenerator();
        $idTokenVerifier = $this->createIdTokenVerifier();
        $sessionCookieVerifier = $this->createSessionCookieVerifier();
        $signInHandler = new CVendor_Firebase_Auth_SignIn_GuzzleHandler($httpClient);

        return new CVendor_Firebase_Auth($authApiClient, $httpClient, $customTokenGenerator, $idTokenVerifier, $sessionCookieVerifier, $signInHandler, $projectId, $tenantId, $this->clock);
    }

    /**
     * @return null|CVendor_Firebase_JWT_CustomTokenGenerator|CVendor_Firebase_Auth_CustomTokenViaGoogleIam
     */
    private function createCustomTokenGenerator() {
        $serviceAccount = $this->getServiceAccount();
        $clientEmail = $this->getClientEmail();
        $privateKey = $serviceAccount !== null ? $serviceAccount->getPrivateKey() : null;

        if ($clientEmail && $privateKey) {
            $generator = CVendor_Firebase_JWT_CustomTokenGenerator::withClientEmailAndPrivateKey($clientEmail, $privateKey);

            if ($this->tenantId !== null) {
                $generator = $generator->withTenantId($this->tenantId);
            }

            return $generator;
        }

        if ($clientEmail !== null) {
            return new CVendor_Firebase_Auth_CustomTokenViaGoogleIam($clientEmail, $this->createApiClient(), $this->tenantId);
        }

        return null;
    }

    /**
     * @return CVendor_Firebase_JWT_IdTokenVerifier
     */
    private function createIdTokenVerifier() {
        $verifier = CVendor_Firebase_JWT_IdTokenVerifier::createWithProjectIdAndCache($this->getProjectId(), $this->verifierCache);

        if ($this->tenantId !== null) {
            $verifier = $verifier->withExpectedTenantId($this->tenantId);
        }

        return $verifier;
    }

    /**
     * @return CVendor_Firebase_JWT_SessionCookieVerifier
     */
    private function createSessionCookieVerifier() {
        return CVendor_Firebase_JWT_SessionCookieVerifier::createWithProjectIdAndCache($this->getProjectId(), $this->verifierCache);
    }

    public function createMessaging() {
        $projectId = $this->getServiceAccount()->getProjectId();

        $messagingApiClient = new CVendor_Firebase_Messaging_ApiClient(
            $this->createApiClient([
                'base_uri' => 'https://fcm.googleapis.com/v1/projects/' . $projectId,
            ])
        );

        $appInstanceApiClient = new CVendor_Firebase_Messaging_AppInstanceApiClient(
            $this->createApiClient([
                'base_uri' => 'https://iid.googleapis.com',
                'headers' => [
                    'access_token_auth' => 'true',
                ],
            ])
        );

        return new CVendor_Firebase_Messaging($messagingApiClient, $appInstanceApiClient, $projectId);
    }

    protected function createGoogleAuthTokenMiddleware() {
        $serviceAccount = $this->getServiceAccount();

        // @codeCoverageIgnoreStart
        if ($serviceAccount->hasClientId() && $serviceAccount->hasPrivateKey()) {
            $credentials = new ServiceAccountCredentials(self::API_CLIENT_SCOPES, [
                'client_email' => $serviceAccount->getClientEmail(),
                'client_id' => $serviceAccount->getClientId(),
                'private_key' => $serviceAccount->getPrivateKey(),
            ]);
        } elseif ((new GcpMetadata())->isAvailable()) {
            // We can't test this programatically when not on GCE/GCP
            $credentials = new GCECredentials();
        } else {
            throw new RuntimeException('Unable to determine credentials.');
        }
        // @codeCoverageIgnoreEnd

        return new AuthTokenMiddleware($credentials);
    }

    public function createApiClient(array $config = null) {
        if ($config == null) {
            $config = [];
        }

        // If present, the config given to this method override fields passed to withHttpClientConfig()
        $config = \array_merge($this->httpClientConfig, $config);

        $googleAuthTokenMiddleware = $this->createGoogleAuthTokenMiddleware();

        $handler = isset($config['handler']) ? $config['handler'] : null;

        if (!($handler instanceof HandlerStack)) {
            $handler = HandlerStack::create($handler);
        }

        foreach ($this->httpClientMiddlewares as $middleware) {
            $handler->push($middleware);
        }

        $handler->push($googleAuthTokenMiddleware);
        $handler->push(CVendor_Firebase_Http_Middleware::responseWithSubResponses());

        $config['handler'] = $handler;
        $config['auth'] = 'google_auth';

        return new Client($config);
    }

    /**
     * @return null|CredentialsLoader
     */
    private function getGoogleAuthTokenCredentials(): ?CredentialsLoader {
        if ($this->googleAuthTokenCredentials !== null) {
            return $this->googleAuthTokenCredentials;
        }

        $serviceAccount = $this->getServiceAccount();

        if ($serviceAccount !== null) {
            return $this->googleAuthTokenCredentials = new ServiceAccountCredentials(self::API_CLIENT_SCOPES, $serviceAccount->asArray());
        }

        if ($this->discoveryIsDisabled) {
            return null;
        }

        try {
            return $this->googleAuthTokenCredentials = ApplicationDefaultCredentials::getCredentials(self::API_CLIENT_SCOPES);
        } catch (Throwable $e) {
            return null;
        }
    }
}
