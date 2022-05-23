<?php

use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @internal
 */
final class CVendor_Firebase_Auth_SignIn_GuzzleHandler implements CVendor_Firebase_Auth_SignIn_HandlerInterface {
    /**
     * @var array<string, mixed>
     */
    private static $defaultBody = [
        'returnSecureToken' => true,
    ];

    /**
     * @var array<string, mixed>
     */
    private static $defaultHeaders = [
        'Content-Type' => 'application/json; charset=UTF-8',
    ];

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param CVendor_Firebase_Auth_SignInInterface $action
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function handle(CVendor_Firebase_Auth_SignInInterface $action) {
        $request = $this->createApiRequest($action);

        try {
            $response = $this->client->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {
            throw CVendor_Firebase_Auth_Exception_FailedToSignInException::fromPrevious($e);
        }

        if ($response->getStatusCode() !== 200) {
            throw CVendor_Firebase_Auth_Exception_FailedToSignInException::withActionAndResponse($action, $response);
        }

        try {
            $data = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);
        } catch (\InvalidArgumentException $e) {
            throw CVendor_Firebase_Auth_Exception_FailedToSignInException::fromPrevious($e);
        }

        return CVendor_Firebase_Auth_SignInResult::fromData($data);
    }

    /**
     * @param CVendor_Firebase_Auth_SignInInterface $action
     *
     * @return RequestInterface
     */
    private function createApiRequest(CVendor_Firebase_Auth_SignInInterface $action) {
        switch (true) {
            case $action instanceof CVendor_Firebase_Auth_SignInAnonymously:
                return $this->anonymous($action);
            case $action instanceof CVendor_Firebase_Auth_SignInWithCustomToken:
                return $this->customToken($action);
            case $action instanceof CVendor_Firebase_Auth_SignInWithEmailAndPassword:
                return $this->emailAndPassword($action);
            case $action instanceof CVendor_Firebase_Auth_SignInWithEmailAndOobCode:
                return $this->emailAndOobCode($action);
            case $action instanceof CVendor_Firebase_Auth_SignInWithIdpCredentials:
                return $this->idpCredentials($action);
            case $action instanceof CVendor_Firebase_Auth_SignInWithRefreshToken:
                return $this->refreshToken($action);
            default:
                throw new CVendor_Firebase_Auth_Exception_FailedToSignInException(self::class . ' does not support ' . \get_class($action));
        }
    }

    /**
     * @param CVendor_Firebase_Auth_SignInAnonymously $action
     *
     * @return Request
     */
    private function anonymous(CVendor_Firebase_Auth_SignInAnonymously $action) {
        $uri = Utils::uriFor('https://identitytoolkit.googleapis.com/v1/accounts:signUp');

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode(self::prepareBody($action), JSON_FORCE_OBJECT));

        $headers = self::$defaultHeaders;

        return new Request('POST', $uri, $headers, $body);
    }

    /**
     * @param CVendor_Firebase_Auth_SignInWithCustomToken $action
     *
     * @return Request
     */
    private function customToken(CVendor_Firebase_Auth_SignInWithCustomToken $action) {
        $uri = Utils::uriFor('https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken');

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode(\array_merge(self::prepareBody($action), [
            'token' => $action->customToken(),
        ]), JSON_FORCE_OBJECT));

        $headers = self::$defaultHeaders;

        return new Request('POST', $uri, $headers, $body);
    }

    /**
     * @param CVendor_Firebase_Auth_SignInWithEmailAndPassword $action
     *
     * @return Request
     */
    private function emailAndPassword(CVendor_Firebase_Auth_SignInWithEmailAndPassword $action) {
        $uri = Utils::uriFor('https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword');

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode(\array_merge(self::prepareBody($action), [
            'email' => $action->email(),
            'password' => $action->clearTextPassword(),
            'returnSecureToken' => true,
        ]), JSON_FORCE_OBJECT));

        $headers = self::$defaultHeaders;

        return new Request('POST', $uri, $headers, $body);
    }

    /**
     * @param CVendor_Firebase_Auth_SignInWithEmailAndOobCode $action
     *
     * @return Request
     */
    private function emailAndOobCode(CVendor_Firebase_Auth_SignInWithEmailAndOobCode $action) {
        $uri = Utils::uriFor('https://www.googleapis.com/identitytoolkit/v3/relyingparty/emailLinkSignin');

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode(\array_merge(self::prepareBody($action), [
            'email' => $action->email(),
            'oobCode' => $action->oobCode(),
            'returnSecureToken' => true,
        ]), JSON_FORCE_OBJECT));

        $headers = self::$defaultHeaders;

        return new Request('POST', $uri, $headers, $body);
    }

    /**
     * @param CVendor_Firebase_Auth_SignInWithIdpCredentials $action
     *
     * @return Request
     */
    private function idpCredentials(CVendor_Firebase_Auth_SignInWithIdpCredentials $action) {
        $uri = Utils::uriFor('https://identitytoolkit.googleapis.com/v1/accounts:signInWithIdp');

        $postBody = [
            'access_token' => $action->accessToken(),
            'id_token' => $action->idToken(),
            'providerId' => $action->provider(),
        ];

        if ($oauthTokenSecret = $action->oauthTokenSecret()) {
            $postBody['oauth_token_secret'] = $oauthTokenSecret;
        }

        if ($rawNonce = $action->rawNonce()) {
            $postBody['nonce'] = $rawNonce;
        }

        $rawBody = \array_merge(self::prepareBody($action), [
            'postBody' => \http_build_query($postBody),
            'returnIdpCredential' => true,
            'requestUri' => $action->requestUri(),
        ]);

        if ($action->linkingIdToken()) {
            $rawBody['idToken'] = $action->linkingIdToken();
        }

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode($rawBody, JSON_FORCE_OBJECT));

        $headers = self::$defaultHeaders;

        return new Request('POST', $uri, $headers, $body);
    }

    /**
     * @param CVendor_Firebase_Auth_SignInWithRefreshToken $action
     *
     * @return Request
     */
    private function refreshToken(CVendor_Firebase_Auth_SignInWithRefreshToken $action) {
        $body = Query::build([
            'grant_type' => 'refresh_token',
            'refresh_token' => $action->refreshToken(),
        ]);

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ];

        $uri = Utils::uriFor('https://securetoken.googleapis.com/v1/token');

        return new Request('POST', $uri, $headers, $body);
    }

    /**
     * @return array<string, mixed>
     */
    private static function prepareBody(CVendor_Firebase_Auth_SignInInterface $action) {
        $body = self::$defaultBody;

        if ($action instanceof CVendor_Firebase_Auth_IsTenantAwareInterface && $tenantId = $action->tenantId()) {
            $body['tenantId'] = $tenantId;
        }

        return $body;
    }
}
