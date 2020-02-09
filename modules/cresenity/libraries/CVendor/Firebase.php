<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Google\Auth\Credentials\GCECredentials;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Middleware\AuthTokenMiddleware;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Storage\StorageClient;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

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
     * @var ServiceAccount|null
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
    public function __construct($config = []) {
        $this->config = $config;
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

    protected function createGoogleAuthTokenMiddleware()
    {
        $serviceAccount = $this->getServiceAccount();

        if ($serviceAccount->hasClientId() && $serviceAccount->hasPrivateKey()) {
            $credentials = new ServiceAccountCredentials(self::API_CLIENT_SCOPES, [
                'client_email' => $serviceAccount->getClientEmail(),
                'client_id' => $serviceAccount->getClientId(),
                'private_key' => $serviceAccount->getPrivateKey(),
            ]);
        } elseif ((new GcpMetadata())->isAvailable()) {
            // @codeCoverageIgnoreStart
            // We can't test this programatically when not on GCE/GCP
            $credentials = new GCECredentials();
        // @codeCoverageIgnoreEnd
        } else {
            throw new RuntimeException('Unable to determine credentials.');
        }

        return new AuthTokenMiddleware($credentials);
    }
    
    
    
}
