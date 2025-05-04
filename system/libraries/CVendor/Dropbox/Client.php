<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class CVendor_Dropbox_Client {
    protected static $baseUrl = 'https://api.dropboxapi.com/2/';

    protected static $contentUrl = 'https://content.dropboxapi.com/2/';

    protected static $authorizeUrl = 'https://www.dropbox.com/oauth2/authorize';

    protected static $tokenUrl = 'https://api.dropbox.com/oauth2/token';

    protected $clientId;

    protected $clientSecret;

    protected $accessToken;

    protected $refreshToken;

    protected $expiredIn;

    protected $accessTokenCacheKey;

    protected $useCache;

    protected $redirectUri;

    protected $scopes;

    protected $accessType;

    protected $tokenData;

    protected $landingUri;

    protected $options;

    public function __construct($clientId, $clientSecret, $options = []) {
        $this->options = $options;
        $accessToken = carr::get($options, 'accessToken');
        $expiredIn = null;
        if ($accessToken) {
            $expiredIn = c::now()->addYears(100);
        }
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
        $this->refreshToken = null;
        $this->expiredIn = $expiredIn;
        $this->accessTokenCacheKey = 'dropbox.token.' . $clientId;
        $this->useCache = carr::get($options, 'useCache', true);
        $this->redirectUri = carr::get($options, 'redirectUri', CF::config('vendor.dropbox.redirect_uri'));
        $this->landingUri = carr::get($options, 'landingUri', CF::config('vendor.dropbox.landing_uri'));
        $this->scopes = carr::get($options, 'scopes', CF::config('vendor.dropbox.scopes'));
        $this->accessType = carr::get($options, 'accessType', CF::config('vendor.dropbox.access_type'));
        $this->tokenData = [];
        $this->loadFromCache();
    }

    public function files() {
        return new CVendor_Dropbox_Files($this->clientId, $this->clientSecret, $this->options);
    }

    /**
     * @return \CCache_Repository
     */
    protected function cache() {
        return CCache::manager()->store();
    }

    public function getCurrentAccount() {
        return $this->post('users/get_current_account');
    }

    /**
     * Return authenticated access token or request new token when expired.
     *
     * @param $returnNullNoAccessToken null when set to true return null
     *
     * @return return string access token
     */
    public function getAccessToken($returnNullNoAccessToken = null) {
        if ($this->expiredIn != null && $this->expiredIn <= c::now()) {
            if ($this->refreshToken) {
                $params = [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $this->refreshToken,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret
                ];
                $tokenResponse = $this->dopost(self::$tokenUrl, $params);
                $this->accessToken = $tokenResponse['access_token'];
                $this->expiredIn = c::now()->addSeconds($tokenResponse['expires_in']);
                $this->saveToCache();
            }
        }

        return $this->accessToken;
    }

    /**
     * Run guzzle to process requested url.
     *
     * @param            $type          string
     * @param            $request       string
     * @param            $data          array
     * @param null|mixed $customHeaders
     * @param mixed      $useToken
     *
     * @return null|array
     */
    protected function guzzle($type, $request, $data = [], $customHeaders = null, $useToken = true) {
        try {
            $client = new Client();

            $headers = [
                'content-type' => 'application/json'
            ];

            if ($useToken == true) {
                $headers['Authorization'] = 'Bearer ' . $this->getAccessToken();
            }

            if ($customHeaders !== null) {
                foreach ($customHeaders as $key => $value) {
                    $headers[$key] = $value;
                }
            }

            $response = $client->$type(self::$baseUrl . $request, [
                'headers' => $headers,
                'body' => json_encode($data)
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            throw new Exception($e->getResponse()->getBody()->getContents());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function saveToCache() {
        if ($this->useCache) {
            $cacheData = [
                'accessToken' => $this->accessToken,
                'expiredIn' => $this->expiredIn,
                'refreshToken' => $this->refreshToken,
                'tokenData' => $this->tokenData
            ];
            $this->cache()->put($this->accessTokenCacheKey, $cacheData);
        }
    }

    protected function loadFromCache() {
        if ($this->useCache) {
            $cacheData = $this->cache()->get($this->accessTokenCacheKey);
            if ($cacheData) {
                $this->accessToken = $cacheData['accessToken'];
                $this->expiredIn = $cacheData['expiredIn'];
                $this->refreshToken = $cacheData['refreshToken'];
                $this->tokenData = $cacheData['tokenData'];
            }
        }
    }

    /**
     * Make a connection or return a token where it's valid.
     *
     * @return mixed
     */
    public function connect() {
        //when no code param redirect to Microsoft
        if (!c::request()->has('code')) {
            $url = self::$authorizeUrl . '?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'scope' => $this->scopes,
                'token_access_type' => $this->accessType
            ]);

            return c::redirect()->away($url);
        } elseif (c::request()->has('code')) {
            // With the authorization code, we can retrieve access tokens and other data.
            try {
                $params = [
                    'grant_type' => 'authorization_code',
                    'code' => c::request('code'),
                    'redirect_uri' => $this->redirectUri,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret
                ];

                $token = $this->dopost(self::$tokenUrl, $params);

                //get user details
                $me = $this->post('users/get_current_account');
                //find record and add email - not required but useful none the less

                $result = $this->storeToken($token, $me);

                return c::redirect($this->landingUri);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * @return object
     */
    public function isConnected() {
        return is_array($this->tokenData) && isset($this->tokenData['uid']);
    }

    protected function forceStartingSlash($string) {
        if (substr($string, 0, 1) !== '/') {
            $string = "/$string";
        }

        return $string;
    }

    /**
     * Store token.
     *
     * @param array      $token
     * @param null|mixed $me
     *
     * @return object
     */
    protected function storeToken($token, $me = null) {
        $this->accessToken = $token['access_token'];
        $this->expiredIn = c::now()->addSeconds($token['expires_in']);
        $tokenData = [];
        $tokenData['uid'] = $token['uid'];
        $tokenData['account_id'] = $token['account_id'];
        $tokenData['scope'] = $token['scope'];
        $tokenData['token_type'] = $token['token_type'];

        if ($me && is_array($me)) {
            $tokenData['email'] = $me['email'];
        }
        $this->tokenData = $tokenData;

        if (isset($token['refresh_token'])) {
            $this->refreshToken = $token['refresh_token'];
        }
    }

    protected static function dopost($url, $params) {
        try {
            $client = new Client();
            $response = $client->post($url, ['form_params' => $params]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            return json_decode($e->getResponse()->getBody()->getContents(), true);
        }
    }

    /**
     * __call catches all requests when no found method is requested.
     *
     * @param $function - the verb to execute
     * @param $args     - array of arguments
     *
     * @return gizzle request
     */
    public function __call($function, $args) {
        $options = ['get', 'post', 'patch', 'put', 'delete'];
        $path = (isset($args[0])) ? $args[0] : null;
        $data = (isset($args[1])) ? $args[1] : null;
        $customHeaders = (isset($args[2])) ? $args[2] : null;
        $useToken = (isset($args[3])) ? $args[3] : true;

        if (in_array($function, $options)) {
            return self::guzzle($function, $path, $data, $customHeaders, $useToken);
        } else {
            //request verb is not in the $options array
            throw new Exception($function . ' is not a valid HTTP Verb');
        }
    }
}
