<?php

use Lcobucci\JWT\Token;
use Psr\Clock\ClockInterface;
use GuzzleHttp\ClientInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class CVendor_Firebase_Auth implements CVendor_Firebase_Contract_AuthInterface {
    /**
     * @var CVendor_Firebase_Auth_ApiClient
     */
    private $client;

    private ClientInterface $httpClient;

    /**
     * @var null|CustomTokenGenerator|CustomTokenViaGoogleIam
     */
    private $tokenGenerator;

    /**
     * @var CVendor_Firebase_JWT_IdTokenVerifier
     */
    private $idTokenVerifier;

    /**
     * @var CVendor_Firebase_JWT_SessionCookieVerifier
     */
    private $sessionCookieVerifier;

    /**
     * @var CVendor_Firebase_Auth_SignIn_HandlerInterface
     */
    private $signInHandler;

    /**
     * @var null|string
     */
    private $tenantId;

    /**
     * @var string
     */
    private $projectId;

    private ClockInterface $clock;

    /**
     * @param null|CVendor_Firebase_JWT_CustomTokenGenerator|CustomTokenViaGoogleIam $tokenGenerator
     * @param string                                                                 $projectId
     * @param null|string                                                            $tenantId
     */
    public function __construct(
        CVendor_Firebase_Auth_ApiClient $client,
        ClientInterface $httpClient,
        $tokenGenerator,
        CVendor_Firebase_JWT_IdTokenVerifier $idTokenVerifier,
        CVendor_Firebase_JWT_SessionCookieVerifier $sessionCookieVerifier,
        CVendor_Firebase_Auth_SignIn_HandlerInterface $signInHandler,
        $projectId,
        $tenantId,
        ClockInterface $clock
    ) {
        $this->client = $client;
        $this->httpClient = $httpClient;
        $this->tokenGenerator = $tokenGenerator;
        $this->idTokenVerifier = $idTokenVerifier;
        $this->sessionCookieVerifier = $sessionCookieVerifier;
        $this->signInHandler = $signInHandler;
        $this->tenantId = $tenantId;
        $this->projectId = $projectId;
        $this->clock = $clock;
    }

    /**
     * @param mixed $uid
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function getUser($uid) {
        $uid = (string) (new CVendor_Firebase_Value_Uid((string) $uid));

        $userRecord = $this->getUsers([$uid])[$uid] ?? null;

        if ($userRecord !== null) {
            return $userRecord;
        }

        throw new CVendor_Firebase_Auth_Exception_UserNotFoundException("No user with uid '{$uid}' found.");
    }

    /**
     * @param array $uids
     *
     * @return array
     */
    public function getUsers(array $uids) {
        $uids = \array_map(static function ($uid) {
            return (string) (new CVendor_Firebase_Value_Uid((string) $uid));
        }, $uids);

        $users = \array_fill_keys($uids, null);

        $response = $this->client->getAccountInfo($uids);

        $data = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);

        foreach ($data['users'] ?? [] as $userData) {
            $userRecord = CVendor_Firebase_Auth_UserRecord::fromResponseData($userData);
            $users[$userRecord->uid] = $userRecord;
        }

        return $users;
    }

    /**
     * @param int $maxResults
     * @param int $batchSize
     *
     * @return Traversable
     */
    public function listUsers($maxResults = 1000, $batchSize = 1000) {
        $pageToken = null;
        $count = 0;

        do {
            $response = $this->client->downloadAccount($batchSize, $pageToken);
            $result = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);

            foreach ((array) ($result['users'] ?? []) as $userData) {
                yield CVendor_Firebase_Auth_UserRecord::fromResponseData($userData);

                if (++$count === $maxResults) {
                    return;
                }
            }

            $pageToken = $result['nextPageToken'] ?? null;
        } while ($pageToken);
    }

    /**
     * @param mixed $properties
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function createUser($properties) {
        $request = $properties instanceof CVendor_Firebase_Request_CreateUser
            ? $properties
            : CVendor_Firebase_Request_CreateUser::withProperties($properties);

        $response = $this->client->createUser($request);

        return $this->getUserRecordFromResponse($response);
    }

    /**
     * @param mixed $uid
     * @param mixed $properties
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function updateUser($uid, $properties) {
        $request = $properties instanceof CVendor_Firebase_Request_UpdateUser
            ? $properties
            : CVendor_Firebase_Request_UpdateUser::withProperties($properties);

        $request = $request->withUid($uid);

        $response = $this->client->updateUser($request);

        return $this->getUserRecordFromResponse($response);
    }

    /**
     * @param mixed $email
     * @param mixed $password
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function createUserWithEmailAndPassword($email, $password) {
        return $this->createUser(
            CVendor_Firebase_Request_CreateUser::new()
                ->withUnverifiedEmail($email)
                ->withClearTextPassword($password)
        );
    }

    /**
     * @param mixed $email
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function getUserByEmail($email) {
        $email = (string) (new CVendor_Firebase_Value_Email((string) $email));

        $response = $this->client->getUserByEmail($email);

        $data = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);

        if (empty($data['users'][0])) {
            throw new CVendor_Firebase_Auth_Exception_UserNotFoundException("No user with email '{$email}' found.");
        }

        return CVendor_Firebase_Auth_UserRecord::fromResponseData($data['users'][0]);
    }

    /**
     * @param mixed $phoneNumber
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function getUserByPhoneNumber($phoneNumber) {
        $phoneNumber = (string) $phoneNumber;

        $response = $this->client->getUserByPhoneNumber($phoneNumber);

        $data = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);

        if (empty($data['users'][0])) {
            throw new CVendor_Firebase_Auth_Exception_UserNotFoundException("No user with phone number '{$phoneNumber}' found.");
        }

        return CVendor_Firebase_Auth_UserRecord::fromResponseData($data['users'][0]);
    }

    /**
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function createAnonymousUser() {
        return $this->createUser(CVendor_Firebase_Request_CreateUser::new());
    }

    /**
     * @param mixed $uid
     * @param mixed $newPassword
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function changeUserPassword($uid, $newPassword) {
        return $this->updateUser($uid, CVendor_Firebase_Request_UpdateUser::new()->withClearTextPassword($newPassword));
    }

    /**
     * @param mixed $uid
     * @param mixed $newEmail
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function changeUserEmail($uid, $newEmail) {
        return $this->updateUser($uid, CVendor_Firebase_Request_UpdateUser::new()->withEmail($newEmail));
    }

    /**
     * @param mixed $uid
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function enableUser($uid) {
        return $this->updateUser($uid, CVendor_Firebase_Request_UpdateUser::new()->markAsEnabled());
    }

    /**
     * @param mixed $uid
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function disableUser($uid) {
        return $this->updateUser($uid, CVendor_Firebase_Request_UpdateUser::new()->markAsDisabled());
    }

    public function deleteUser($uid): void {
        $uid = (string) (new CVendor_Firebase_Value_Uid((string) $uid));

        try {
            $this->client->deleteUser($uid);
        } catch (CVendor_Firebase_Auth_Exception_UserNotFoundException $e) {
            throw new CVendor_Firebase_Auth_Exception_UserNotFoundException("No user with uid '{$uid}' found.");
        }
    }

    /**
     * Undocumented function.
     *
     * @param iterable $uids
     * @param bool     $forceDeleteEnabledUsers
     *
     * @return CVendor_Firebase_Auth_DeleteUsersResult
     */
    public function deleteUsers($uids, $forceDeleteEnabledUsers = false) {
        $request = CVendor_Firebase_Auth_DeleteUsersRequest::withUids($this->projectId, $uids, $forceDeleteEnabledUsers);

        $response = $this->client->deleteUsers(
            $request->projectId(),
            $request->uids(),
            $request->enabledUsersShouldBeForceDeleted(),
            $this->tenantId
        );

        return CVendor_Firebase_Auth_DeleteUsersResult::fromRequestAndResponse($request, $response);
    }

    /**
     * @param string      $type
     * @param mixed       $email
     * @param mixed       $actionCodeSettings
     * @param null|string $locale
     *
     * @return string
     */
    public function getEmailActionLink($type, $email, $actionCodeSettings = null, $locale = null) {
        $email = (string) (new CVendor_Firebase_Value_Email((string) $email));

        if ($actionCodeSettings === null) {
            $actionCodeSettings = CVendor_Firebase_Auth_ActionCodeSettings_ValidatedActionCodeSettings::empty();
        } else {
            $actionCodeSettings = $actionCodeSettings instanceof CVendor_Firebase_Auth_ActionCodeSettingsInterface
                ? $actionCodeSettings
                : CVendor_Firebase_Auth_ActionCodeSettings_ValidatedActionCodeSettings::fromArray($actionCodeSettings);
        }

        return (new CVendor_Firebase_Auth_CreateActionLink_GuzzleApiClientHandler($this->httpClient, $this->projectId))
            ->handle(CVendor_Firebase_Auth_CreateActionLink::new($type, $email, $actionCodeSettings, $this->tenantId, $locale));
    }

    public function sendEmailActionLink($type, $email, $actionCodeSettings = null, $locale = null) {
        $email = (string) (new CVendor_Firebase_Value_Email((string) $email));

        if ($actionCodeSettings === null) {
            $actionCodeSettings = CVendor_Firebase_Auth_ActionCodeSettings_ValidatedActionCodeSettings::empty();
        } else {
            $actionCodeSettings = $actionCodeSettings instanceof CVendor_Firebase_Auth_ActionCodeSettingsInterface
                ? $actionCodeSettings
                : CVendor_Firebase_Auth_ActionCodeSettings_ValidatedActionCodeSettings::fromArray($actionCodeSettings);
        }

        $createAction = CVendor_Firebase_Auth_CreateActionLink::new($type, $email, $actionCodeSettings, $this->tenantId, $locale);
        $sendAction = new CVendor_Firebase_Auth_SendActionLink($createAction, $locale);

        if (\mb_strtolower($type) === 'verify_email') {
            // The Firebase API expects an ID token for the user belonging to this email address
            // see https://github.com/firebase/firebase-js-sdk/issues/1958
            try {
                $user = $this->getUserByEmail($email);
            } catch (Throwable $e) {
                throw new CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException($e->getMessage(), $e->getCode(), $e);
            }

            try {
                $signInResult = $this->signInAsUser($user);
            } catch (Throwable $e) {
                throw new CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException($e->getMessage(), $e->getCode(), $e);
            }

            if (!($idToken = $signInResult->idToken())) {
                // @codeCoverageIgnoreStart
                // This only happens if the response on Google's side has changed
                // If it does, the tests will fail, but we don't have to cover that
                throw new CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException("Failed to send action link: Unable to retrieve ID token for user assigned to email {$email}");
                // @codeCoverageIgnoreEnd
            }

            $sendAction = $sendAction->withIdTokenString($idToken);
        }

        (new CVendor_Firebase_Auth_SendActionLink_GuzzleApiClientHandler($this->httpClient, $this->projectId))->handle($sendAction);
    }

    public function getEmailVerificationLink($email, $actionCodeSettings = null, $locale = null) {
        return $this->getEmailActionLink('VERIFY_EMAIL', $email, $actionCodeSettings, $locale);
    }

    public function sendEmailVerificationLink($email, $actionCodeSettings = null, $locale = null) {
        $this->sendEmailActionLink('VERIFY_EMAIL', $email, $actionCodeSettings, $locale);
    }

    public function getPasswordResetLink($email, $actionCodeSettings = null, $locale = null) {
        return $this->getEmailActionLink('PASSWORD_RESET', $email, $actionCodeSettings, $locale);
    }

    public function sendPasswordResetLink($email, $actionCodeSettings = null, $locale = null) {
        $this->sendEmailActionLink('PASSWORD_RESET', $email, $actionCodeSettings, $locale);
    }

    public function getSignInWithEmailLink($email, $actionCodeSettings = null, $locale = null) {
        return $this->getEmailActionLink('EMAIL_SIGNIN', $email, $actionCodeSettings, $locale);
    }

    public function sendSignInWithEmailLink($email, $actionCodeSettings = null, $locale = null) {
        $this->sendEmailActionLink('EMAIL_SIGNIN', $email, $actionCodeSettings, $locale);
    }

    public function setCustomUserClaims($uid, array $claims) {
        $uid = (string) (new CVendor_Firebase_Value_Uid((string) $uid));
        $claims ??= [];

        $this->client->setCustomUserClaims($uid, $claims);
    }

    /**
     * @param mixed $uid
     * @param array $claims
     * @param int   $ttl
     *
     * @return UnencryptedToken
     */
    public function createCustomToken($uid, array $claims = [], $ttl = 3600) {
        $uid = (string) (new CVendor_Firebase_Value_Uid((string) $uid));

        $generator = $this->tokenGenerator;

        if ($generator instanceof CVendor_Firebase_JWT_CustomTokenGenerator) {
            $tokenString = $generator->createCustomToken($uid, $claims, $ttl)->toString();
        } elseif ($generator instanceof CVendor_Firebase_Auth_CustomTokenViaGoogleIam) {
            $expiresAt = $this->clock->now()->add(CVendor_Firebase_JWT_Value_Duration::make($ttl)->value());

            $tokenString = $generator->createCustomToken($uid, $claims, $expiresAt)->toString();
        } else {
            throw new CVendor_Firebase_Auth_Exception_AuthErrorException('Custom Token Generation is disabled because the current credentials do not permit it');
        }

        return $this->parseToken($tokenString);
    }

    /**
     * @param string $tokenString
     *
     * @return UnencryptedToken
     */
    public function parseToken($tokenString) {
        try {
            $parsedToken = Configuration::forUnsecuredSigner()->parser()->parse($tokenString);
            \assert($parsedToken instanceof UnencryptedToken);
        } catch (Throwable $e) {
            throw new InvalidArgumentException('The given token could not be parsed: ' . $e->getMessage());
        }

        return $parsedToken;
    }

    /**
     * @param mixed    $idToken
     * @param bool     $checkIfRevoked
     * @param null|int $leewayInSeconds
     *
     * @return UnencryptedToken
     */
    public function verifyIdToken($idToken, $checkIfRevoked = false, $leewayInSeconds = null) {
        $verifier = $this->idTokenVerifier;

        $idTokenString = \is_string($idToken) ? $idToken : $idToken->toString();

        try {
            if ($leewayInSeconds !== null) {
                $verifier->verifyIdTokenWithLeeway($idTokenString, $leewayInSeconds);
            } else {
                $verifier->verifyIdToken($idTokenString);
            }
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToVerifyTokenException($e->getMessage());
        }

        $verifiedToken = $this->parseToken($idTokenString);

        if (!$checkIfRevoked) {
            return $verifiedToken;
        }

        try {
            $user = $this->getUser($verifiedToken->claims()->get('sub'));
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToVerifyTokenException("Error while getting the token's user: {$e->getMessage()}", 0, $e);
        }

        if ($this->userSessionHasBeenRevoked($verifiedToken, $user, $leewayInSeconds)) {
            throw new CVendor_Firebase_Auth_Exception_RevokedIdTokenException($verifiedToken);
        }

        return $verifiedToken;
    }

    /**
     * @param string   $sessionCookie
     * @param bool     $checkIfRevoked
     * @param null|int $leewayInSeconds
     *
     * @return UnencryptedToken
     */
    public function verifySessionCookie($sessionCookie, $checkIfRevoked = false, $leewayInSeconds = null) {
        $verifier = $this->sessionCookieVerifier;

        try {
            if ($leewayInSeconds !== null) {
                $verifier->verifySessionCookieWithLeeway($sessionCookie, $leewayInSeconds);
            } else {
                $verifier->verifySessionCookie($sessionCookie);
            }
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToVerifySessionCookieException($e->getMessage());
        }

        $verifiedSessionCookie = $this->parseToken($sessionCookie);

        if (!$checkIfRevoked) {
            return $verifiedSessionCookie;
        }

        try {
            $user = $this->getUser($verifiedSessionCookie->claims()->get('sub'));
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToVerifySessionCookieException("Error while getting the session cookie's user: {$e->getMessage()}", 0, $e);
        }

        if ($this->userSessionHasBeenRevoked($verifiedSessionCookie, $user, $leewayInSeconds)) {
            throw new CVendor_Firebase_Auth_Exception_RevokedSessionCookieException($verifiedSessionCookie);
        }

        return $verifiedSessionCookie;
    }

    /**
     * @param string $oobCode
     *
     * @return string
     */
    public function verifyPasswordResetCode($oobCode) {
        $response = $this->client->verifyPasswordResetCode($oobCode);

        return CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true)['email'];
    }

    /**
     * @param string $oobCode
     * @param mixed  $newPassword
     * @param bool   $invalidatePreviousSessions
     *
     * @return string
     */
    public function confirmPasswordReset($oobCode, $newPassword, $invalidatePreviousSessions = true) {
        $newPassword = (string) (new CVendor_Firebase_Value_ClearTextPassword((string) $newPassword));

        $response = $this->client->confirmPasswordReset($oobCode, (string) $newPassword);

        $email = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true)['email'];

        if ($invalidatePreviousSessions) {
            $this->revokeRefreshTokens($this->getUserByEmail($email)->uid);
        }

        return $email;
    }

    /**
     * @param mixed $uid
     *
     * @return void
     */
    public function revokeRefreshTokens($uid) {
        $uid = (string) (new CVendor_Firebase_Value_Uid((string) $uid));

        $this->client->revokeRefreshTokens($uid);
    }

    /**
     * @param mixed $uid
     * @param mixed $provider
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function unlinkProvider($uid, $provider) {
        $uid = (string) (new CVendor_Firebase_Value_Uid((string) $uid));
        $provider = \array_map('strval', (array) $provider);

        $response = $this->client->unlinkProvider($uid, $provider);

        return $this->getUserRecordFromResponse($response);
    }

    /**
     * @param mixed      $user
     * @param null|array $claims
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInAsUser($user, ?array $claims = null) {
        $claims ??= [];
        $uid = $user instanceof CVendor_Firebase_Auth_UserRecord ? $user->uid : (string) $user;

        try {
            $customToken = $this->createCustomToken($uid, $claims);
        } catch (Throwable $e) {
            throw CVendor_Firebase_Auth_Exception_FailedToSignInException::fromPrevious($e);
        }

        $action = CVendor_Firebase_Auth_SignInWithCustomToken::fromValue($customToken->toString());

        if ($this->tenantId !== null) {
            $action = $action->withTenantId($this->tenantId);
        }

        return $this->signInHandler->handle($action);
    }

    /**
     * @param mixed $token
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithCustomToken($token) {
        $token = $token instanceof Token ? $token->toString() : $token;

        $action = CVendor_Firebase_Auth_SignInWithCustomToken::fromValue($token);

        if ($this->tenantId !== null) {
            $action = $action->withTenantId($this->tenantId);
        }

        return $this->signInHandler->handle($action);
    }

    /**
     * @param string $refreshToken
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithRefreshToken($refreshToken) {
        $action = CVendor_Firebase_Auth_SignInWithRefreshToken::fromValue($refreshToken);

        if ($this->tenantId !== null) {
            $action = $action->withTenantId($this->tenantId);
        }

        return $this->signInHandler->handle($action);
    }

    /**
     * @param mixed $email
     * @param mixed $clearTextPassword
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithEmailAndPassword($email, $clearTextPassword) {
        $email = (string) (new CVendor_Firebase_Value_Email((string) $email));
        $clearTextPassword = (string) (new CVendor_Firebase_Value_ClearTextPassword((string) $clearTextPassword));

        $action = CVendor_Firebase_Auth_SignInWithEmailAndPassword::fromValues($email, $clearTextPassword);

        if ($this->tenantId !== null) {
            $action = $action->withTenantId($this->tenantId);
        }

        return $this->signInHandler->handle($action);
    }

    /**
     * Undocumented function.
     *
     * @param mixed  $email
     * @param string $oobCode
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithEmailAndOobCode($email, $oobCode) {
        $email = (string) (new CVendor_Firebase_Value_Email((string) $email));

        $action = CVendor_Firebase_Auth_SignInWithEmailAndOobCode::fromValues($email, $oobCode);

        if ($this->tenantId !== null) {
            $action = $action->withTenantId($this->tenantId);
        }

        return $this->signInHandler->handle($action);
    }

    /**
     * Undocumented function.
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInAnonymously() {
        $action = CVendor_Firebase_Auth_SignInAnonymously::new();

        if ($this->tenantId !== null) {
            $action = $action->withTenantId($this->tenantId);
        }

        $result = $this->signInHandler->handle($action);

        if ($result->idToken()) {
            return $result;
        }

        if ($uid = ($result->data()['localId'] ?? null)) {
            return $this->signInAsUser($uid);
        }

        throw new CVendor_Firebase_Auth_Exception_FailedToSignInException('Failed to sign in anonymously: No ID token or UID available');
    }

    /**
     * @param mixed       $provider
     * @param string      $accessToken
     * @param mixed       $redirectUrl
     * @param null|string $oauthTokenSecret
     * @param null|string $linkingIdToken
     * @param null|string $rawNonce
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithIdpAccessToken($provider, $accessToken, $redirectUrl = null, $oauthTokenSecret = null, $linkingIdToken = null, $rawNonce = null) {
        $provider = (string) $provider;
        $redirectUrl = \trim((string) ($redirectUrl ?? 'http://localhost'));
        $linkingIdToken = \trim((string) $linkingIdToken);
        $oauthTokenSecret = \trim((string) $oauthTokenSecret);
        $rawNonce = \trim((string) $rawNonce);

        if ($oauthTokenSecret !== '') {
            $action = CVendor_Firebase_Auth_SignInWithIdpCredentials::withAccessTokenAndOauthTokenSecret($provider, $accessToken, $oauthTokenSecret);
        } else {
            $action = CVendor_Firebase_Auth_SignInWithIdpCredentials::withAccessToken($provider, $accessToken);
        }

        if ($linkingIdToken !== '') {
            $action = $action->withLinkingIdToken($linkingIdToken);
        }

        if ($rawNonce !== '') {
            $action = $action->withRawNonce($rawNonce);
        }

        if ($redirectUrl !== '') {
            $action = $action->withRequestUri($redirectUrl);
        }

        if ($this->tenantId !== null) {
            $action = $action->withTenantId($this->tenantId);
        }

        return $this->signInHandler->handle($action);
    }

    /**
     * @param mixed       $provider
     * @param mixed       $idToken
     * @param mixed       $redirectUrl
     * @param null|string $linkingIdToken
     * @param null|string $rawNonce
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithIdpIdToken($provider, $idToken, $redirectUrl = null, $linkingIdToken = null, $rawNonce = null) {
        $provider = \trim((string) $provider);
        $redirectUrl = \trim((string) ($redirectUrl ?? 'http://localhost'));
        $linkingIdToken = \trim((string) $linkingIdToken);
        $rawNonce = \trim((string) $rawNonce);

        if ($idToken instanceof Token) {
            $idToken = $idToken->toString();
        }

        $action = CVendor_Firebase_Auth_SignInWithIdpCredentials::withIdToken($provider, $idToken);

        if ($rawNonce !== '') {
            $action = $action->withRawNonce($rawNonce);
        }

        if ($linkingIdToken !== '') {
            $action = $action->withLinkingIdToken($linkingIdToken);
        }

        if ($redirectUrl !== '') {
            $action = $action->withRequestUri($redirectUrl);
        }

        if ($this->tenantId !== null) {
            $action = $action->withTenantId($this->tenantId);
        }

        return $this->signInHandler->handle($action);
    }

    /**
     * @param mixed $idToken
     * @param mixed $ttl
     *
     * @return string
     */
    public function createSessionCookie($idToken, $ttl) {
        return (new CVendor_Firebase_Auth_CreateSessionCookie_GuzzleApiClientHandler($this->httpClient, $this->projectId))
            ->handle(CVendor_Firebase_Auth_CreateSessionCookie::forIdToken($idToken, $this->tenantId, $ttl, $this->clock));
    }

    /**
     * Gets the user ID from the response and queries a full UserRecord object for it.
     *
     * @param ResponseInterface $response
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    private function getUserRecordFromResponse(ResponseInterface $response) {
        $uid = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true)['localId'];

        return $this->getUser($uid);
    }

    /**
     * @param UnencryptedToken                 $verifiedToken
     * @param CVendor_Firebase_Auth_UserRecord $user
     * @param null|int                         $leewayInSeconds
     *
     * @return bool
     */
    private function userSessionHasBeenRevoked(UnencryptedToken $verifiedToken, CVendor_Firebase_Auth_UserRecord $user, $leewayInSeconds = null) {
        // The timestamp, in seconds, which marks a boundary, before which Firebase ID token are considered revoked.
        $validSince = $user->tokensValidAfterTime ?? null;

        if (!($validSince instanceof \DateTimeImmutable)) {
            // The user hasn't logged in yet, so there's nothing to revoke
            return false;
        }

        $tokenAuthenticatedAt = CVendor_Firebase_Util_DT::toUTCDateTimeImmutable($verifiedToken->claims()->get('auth_time'));

        if ($leewayInSeconds) {
            $tokenAuthenticatedAt = $tokenAuthenticatedAt->modify('-' . $leewayInSeconds . ' seconds');
        }

        return $tokenAuthenticatedAt->getTimestamp() < $validSince->getTimestamp();
    }
}
