<?php

use Lcobucci\JWT\Token;

use Lcobucci\JWT\UnencryptedToken;
use Psr\Http\Message\UriInterface;

interface CVendor_Firebase_Contract_AuthInterface {
    /**
     * @param \Stringable|string $uid
     *
     * @throws CVendor_Firebase_Auth_Exception_UserNotFoundException
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function getUser($uid);

    /**
     * @param array<\Stringable|string> $uids
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return array<string, null|UserRecord>
     */
    public function getUsers(array $uids);

    /**
     * @param int $maxResults
     * @param int $batchSize
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return Traversable<CVendor_Firebase_Auth_UserRecord>
     */
    public function listUsers($maxResults = 1000, $batchSize = 1000);

    /**
     * Creates a new user with the provided properties.
     *
     * @param array<string, mixed>|Request\CreateUser $properties
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function createUser($properties);

    /**
     * Updates the given user with the given properties.
     *
     * @param \Stringable|string                      $uid
     * @param array<string, mixed>|Request\UpdateUser $properties
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function updateUser($uid, $properties);

    /**
     * @param \Stringable|string $email
     * @param \Stringable|string $password
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function createUserWithEmailAndPassword($email, $password);

    /**
     * @param \Stringable|string $email
     *
     * @throws CVendor_Firebase_Auth_Exception_UserNotFoundException
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function getUserByEmail($email);

    /**
     * @param \Stringable|string $phoneNumber
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function getUserByPhoneNumber($phoneNumber);

    /**
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function createAnonymousUser();

    /**
     * @param \Stringable|string $uid
     * @param \Stringable|string $newPassword
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function changeUserPassword($uid, $newPassword);

    /**
     * @param \Stringable|string $uid
     * @param \Stringable|string $newEmail
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function changeUserEmail($uid, $newEmail);

    /**
     * @param \Stringable|string $uid
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function enableUser($uid);

    /**
     * @param \Stringable|string $uid
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function disableUser($uid);

    /**
     * @param \Stringable|string $uid
     *
     * @throws CVendor_Firebase_Auth_Exception_UserNotFoundException
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     */
    public function deleteUser($uid);

    /**
     * @param iterable<\Stringable|string> $uids
     * @param bool                         $forceDeleteEnabledUsers Whether to force deleting accounts that are not in disabled state. If false, only disabled accounts will be deleted, and accounts that are not disabled will be added to the errors.
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_DeleteUsersResult
     */
    public function deleteUsers(iterable $uids, $forceDeleteEnabledUsers = false);

    /**
     * @param \Stringable|string                                                          $email
     * @param null|CVendor_Firebase_Auth_ActionCodeSettingsInterface|array<string, mixed> $actionCodeSettings
     * @param string                                                                      $type
     * @param null|string                                                                 $locale
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException
     *
     * @return string
     */
    public function getEmailActionLink($type, $email, $actionCodeSettings = null, $locale = null);

    /**
     * @param \Stringable|string                                                          $email
     * @param null|CVendor_Firebase_Auth_ActionCodeSettingsInterface|array<string, mixed> $actionCodeSettings
     * @param string                                                                      $type
     * @param null|string                                                                 $locale
     *
     * @throws CVendor_Firebase_Auth_Exception_UserNotFoundException
     * @throws CVendor_Firebase_Auth_Exception_FailedToSendActionLink
     *
     * @return void
     */
    public function sendEmailActionLink($type, $email, $actionCodeSettings = null, $locale = null);

    /**
     * @param \Stringable|string                                                          $email
     * @param null|CVendor_Firebase_Auth_ActionCodeSettingsInterface|array<string, mixed> $actionCodeSettings
     * @param null|string                                                                 $locale
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException
     *
     * @return string
     */
    public function getEmailVerificationLink($email, $actionCodeSettings = null, $locale = null);

    /**
     * @param \Stringable|string                                                          $email
     * @param null|CVendor_Firebase_Auth_ActionCodeSettingsInterface|array<string, mixed> $actionCodeSettings
     * @param null|string                                                                 $locale
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException
     */
    public function sendEmailVerificationLink($email, $actionCodeSettings = null, $locale = null);

    /**
     * @param \Stringable|string                                                          $email
     * @param null|CVendor_Firebase_Auth_ActionCodeSettingsInterface|array<string, mixed> $actionCodeSettings
     * @param null|string                                                                 $locale
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException
     *
     * @return string
     */
    public function getPasswordResetLink($email, $actionCodeSettings = null, $locale = null);

    /**
     * @param \Stringable|string                                                          $email
     * @param null|CVendor_Firebase_Auth_ActionCodeSettingsInterface|array<string, mixed> $actionCodeSettings
     * @param null|string                                                                 $locale
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException
     *
     * @return void
     */
    public function sendPasswordResetLink($email, $actionCodeSettings = null, $locale = null);

    /**
     * @param \Stringable|string                                                          $email
     * @param null|CVendor_Firebase_Auth_ActionCodeSettingsInterface|array<string, mixed> $actionCodeSettings
     * @param null|string                                                                 $locale
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException
     *
     * @return string
     */
    public function getSignInWithEmailLink($email, $actionCodeSettings = null, $locale = null);

    /**
     * @param \Stringable|string                                                          $email
     * @param null|CVendor_Firebase_Auth_ActionCodeSettingsInterface|array<string, mixed> $actionCodeSettings
     * @param null|string                                                                 $locale
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException
     *
     * @return void
     */
    public function sendSignInWithEmailLink($email, $actionCodeSettings = null, $locale = null);

    /**
     * Sets additional developer claims on an existing user identified by the provided UID.
     *
     * @param \Stringable|string        $uid
     * @param null|array<string, mixed> $claims
     *
     * @see https://firebase.google.com/docs/auth/admin/custom-claims
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     */
    public function setCustomUserClaims($uid, array $claims);

    /**
     * @param \Stringable|string      $uid
     * @param array<string, mixed>    $claims
     * @param int|DateInterval|string $ttl
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return UnencryptedToken
     */
    public function createCustomToken($uid, array $claims = [], $ttl = 3600);

    /**
     * @param string $tokenString
     *
     * @return UnencryptedToken
     */
    public function parseToken($tokenString);

    /**
     * Creates a new Firebase session cookie with the given lifetime.
     *
     * The session cookie JWT will have the same payload claims as the provided ID token.
     *
     * @param Token|string     $idToken The Firebase ID token to exchange for a session cookie
     * @param DateInterval|int $ttl
     *
     * @throws InvalidArgumentException                                             if the token or TTL is invalid
     * @throws CVendor_Firebase_Auth_Exception_FailedToCreateSessionCookieException
     *
     * @return string
     */
    public function createSessionCookie($idToken, $ttl);

    /**
     * Verifies a JWT auth token.
     *
     * Returns a token with the token's claims or rejects it if the token could not be verified.
     *
     * If checkRevoked is set to true, verifies if the session corresponding to the ID token was revoked.
     * If the corresponding user's session was invalidated, a RevokedIdToken exception is thrown.
     * If not specified the check is not applied.
     *
     * NOTE: Allowing time inconsistencies might impose a security risk. Do this only when you are not able
     * to fix your environment's time to be consistent with Google's servers.
     *
     * @param Token|string      $idToken         the JWT to verify
     * @param bool              $checkIfRevoked  whether to check if the ID token is revoked
     * @param null|positive-int $leewayInSeconds number of seconds to allow a token to be expired, in case that there
     *                                           is a clock skew between the signing and the verifying server
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToVerifyTokenException if the token could not be verified
     * @throws CVendor_Firebase_Auth_Exception_RevokedIdTokenException      if the token has been revoked
     *
     * @return UnencryptedToken
     */
    public function verifyIdToken($idToken, $checkIfRevoked = false, $leewayInSeconds = null);

    /**
     * Verifies a JWT session cookie.
     *
     * Returns a token with the cookie's claims or rejects it if the session cookie could not be verified.
     *
     * If checkRevoked is set to true, verifies if the session corresponding to the ID token was revoked.
     * If the corresponding user's session was invalidated, a RevokedSessionCookie exception is thrown.
     * If not specified the check is not applied.
     *
     * NOTE: Allowing time inconsistencies might impose a security risk. Do this only when you are not able
     * to fix your environment's time to be consistent with Google's servers.
     *
     * @param null|positive-int $leewayInSeconds
     * @param string            $sessionCookie
     * @param bool              $checkIfRevoked
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToVerifySessionCookieException
     * @throws CVendor_Firebase_Auth_Exception_RevokedSessionCookieException
     *
     * @return UnencryptedToken
     */
    public function verifySessionCookie($sessionCookie, $checkIfRevoked = false, $leewayInSeconds = null);

    /**
     * Verifies the given password reset code and returns the associated user's email address.
     *
     * @param string $oobCode
     *
     * @see https://firebase.google.com/docs/reference/rest/auth#section-verify-password-reset-code
     *
     * @throws CVendor_Firebase_Auth_Exception_ExpiredOobCodeException
     * @throws CVendor_Firebase_Auth_Exception_InvalidOobCodeException
     * @throws CVendor_Firebase_Auth_Exception_OperationNotAllowedException
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return string
     */
    public function verifyPasswordResetCode($oobCode);

    /**
     * Applies the password reset requested via the given OOB code and returns the associated user's email address.
     *
     * @param string             $oobCode                    the email action code sent to the user's email for resetting the password
     * @param \Stringable|string $newPassword
     * @param bool               $invalidatePreviousSessions Invalidate sessions initialized with the previous credentials
     *
     * @see https://firebase.google.com/docs/reference/rest/auth#section-confirm-reset-password
     *
     * @throws CVendor_Firebase_Auth_Exception_ExpiredOobCodeException
     * @throws CVendor_Firebase_Auth_Exception_InvalidOobCodeException
     * @throws CVendor_Firebase_Auth_Exception_OperationNotAllowedException
     * @throws CVendor_Firebase_Auth_Exception_UserDisabledException
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return string
     */
    public function confirmPasswordReset($oobCode, $newPassword, $invalidatePreviousSessions = true);

    /**
     * Revokes all refresh tokens for the specified user identified by the uid provided.
     * In addition to revoking all refresh tokens for a user, all ID tokens issued
     * before revocation will also be revoked on the Auth backend. Any request with an
     * ID token generated before revocation will be rejected with a token expired error.
     *
     * @param \Stringable|string $uid the user whose tokens are to be revoked
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return void
     */
    public function revokeRefreshTokens($uid);

    /**
     * @param \Stringable|string                        $uid
     * @param \Stringable[]|string[]|\Stringable|string $provider
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return CVendor_Firebase_Auth_UserRecord
     */
    public function unlinkProvider($uid, $provider);

    /**
     * @param UserRecord|\Stringable|string $user
     * @param null|array<string, mixed>     $claims
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInAsUser($user, array $claims = null);

    /**
     * @param Token|string $token
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithCustomToken($token);

    /**
     * @param string $refreshToken
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithRefreshToken($refreshToken);

    /**
     * @param \Stringable|string $email
     * @param \Stringable|string $clearTextPassword
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithEmailAndPassword($email, $clearTextPassword);

    /**
     * @param \Stringable|string $email
     * @param string             $oobCode
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithEmailAndOobCode($email, $oobCode);

    /**
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInAnonymously();

    /**
     * @param \Stringable|string       $provider
     * @param null|UriInterface|string $redirectUrl
     * @param string                   $accessToken
     * @param null|string              $oauthTokenSecret
     * @param null|string              $linkingIdToken
     * @param null|string              $rawNonce
     *
     * @see https://cloud.google.com/identity-platform/docs/reference/rest/v1/accounts/signInWithIdp
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithIdpAccessToken($provider, $accessToken, $redirectUrl = null, $oauthTokenSecret = null, $linkingIdToken = null, $rawNonce = null);

    /**
     * @param \Stringable|string       $provider
     * @param Token|string             $idToken
     * @param null|UriInterface|string $redirectUrl
     * @param null|string              $linkingIdToken
     * @param null|string              $rawNonce
     *
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function signInWithIdpIdToken($provider, $idToken, $redirectUrl = null, $linkingIdToken = null, $rawNonce = null);
}
