<?php

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class CVendor_Firebase_Auth_ApiClient {
    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    /**
     * @var null|string
     */
    private $tenantId;

    /**
     * @var CVendor_Firebase_Auth_ApiExceptionConverter
     */
    private $errorHandler;

    public function __construct(ClientInterface $client, ?string $tenantId = null) {
        $this->client = $client;
        $this->tenantId = $tenantId;
        $this->errorHandler = new CVendor_Firebase_Auth_ApiExceptionConverter();
    }

    /**
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     *
     * @return ResponseInterface
     */
    public function createUser(CVendor_Firebase_Request_CreateUser $request) {
        return $this->requestApi('signupNewUser', $request->jsonSerialize());
    }

    /**
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     *
     * @return ResponseInterface
     */
    public function updateUser(CVendor_Firebase_Request_UpdateUser $request) {
        return $this->requestApi('setAccountInfo', $request->jsonSerialize());
    }

    /**
     * @param string               $uid
     * @param array<string, mixed> $claims
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     *
     * @return ResponseInterface
     */
    public function setCustomUserClaims($uid, array $claims) {
        return $this->requestApi('https://identitytoolkit.googleapis.com/v1/accounts:update', [
            'localId' => $uid,
            'customAttributes' => CVendor_Firebase_Util_JSON::encode($claims, JSON_FORCE_OBJECT),
        ]);
    }

    /**
     * Returns a user for the given email address.
     *
     * @param string $email
     *
     * @throws CVendor_Firebase_Auth_Exception_EmailNotFoundException
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     *
     * @return ResponseInterface
     */
    public function getUserByEmail($email) {
        return $this->requestApi('getAccountInfo', [
            'email' => [$email],
        ]);
    }

    /**
     * @param string $phoneNumber
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     *
     * @return ResponseInterface
     */
    public function getUserByPhoneNumber($phoneNumber) {
        return $this->requestApi('getAccountInfo', [
            'phoneNumber' => [$phoneNumber],
        ]);
    }

    /**
     * @param null|int    $batchSize
     * @param null|string $nextPageToken
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     *
     * @return ResponseInterface
     */
    public function downloadAccount($batchSize = null, $nextPageToken = null) {
        $batchSize ??= 1000;

        return $this->requestApi('downloadAccount', \array_filter([
            'maxResults' => $batchSize,
            'nextPageToken' => $nextPageToken,
        ]));
    }

    /**
     * @param string $uid
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     *
     * @return ResponseInterface
     */
    public function deleteUser($uid) {
        return $this->requestApi('deleteAccount', [
            'localId' => $uid,
        ]);
    }

    /**
     * @param string[] $uids
     *
     * @throws AuthException
     */
    public function deleteUsers(string $projectId, array $uids, bool $forceDeleteEnabledUsers, ?string $tenantId = null): ResponseInterface {
        $data = [
            'localIds' => $uids,
            'force' => $forceDeleteEnabledUsers,
        ];

        if ($tenantId) {
            $data['tenantId'] = $tenantId;
        }

        return $this->requestApi(
            "https://identitytoolkit.googleapis.com/v1/projects/{$projectId}/accounts:batchDelete",
            $data
        );
    }

    /**
     * @param string|array<string> $uids
     *
     * @throws AuthException
     */
    public function getAccountInfo($uids): ResponseInterface {
        if (!\is_array($uids)) {
            $uids = [$uids];
        }

        return $this->requestApi('getAccountInfo', [
            'localId' => $uids,
        ]);
    }

    /**
     * @throws ExpiredOobCode
     * @throws InvalidOobCode
     * @throws OperationNotAllowed
     * @throws AuthException
     */
    public function verifyPasswordResetCode(string $oobCode): ResponseInterface {
        return $this->requestApi('resetPassword', [
            'oobCode' => $oobCode,
        ]);
    }

    /**
     * @throws ExpiredOobCode
     * @throws InvalidOobCode
     * @throws OperationNotAllowed
     * @throws UserDisabled
     * @throws AuthException
     */
    public function confirmPasswordReset(string $oobCode, string $newPassword): ResponseInterface {
        return $this->requestApi('resetPassword', [
            'oobCode' => $oobCode,
            'newPassword' => $newPassword,
        ]);
    }

    /**
     * @throws AuthException
     */
    public function revokeRefreshTokens(string $uid): ResponseInterface {
        return $this->requestApi('setAccountInfo', [
            'localId' => $uid,
            'validSince' => \time(),
        ]);
    }

    /**
     * @param array<int, \Stringable|string> $providers
     *
     * @throws AuthException
     */
    public function unlinkProvider(string $uid, array $providers): ResponseInterface {
        $providers = \array_map('strval', $providers);

        return $this->requestApi('setAccountInfo', [
            'localId' => $uid,
            'deleteProvider' => $providers,
        ]);
    }

    /**
     * @param array<mixed> $data
     *
     * @throws AuthException
     */
    private function requestApi(string $uri, array $data): ResponseInterface {
        $options = [];

        if ($this->tenantId !== null) {
            $data['tenantId'] = $this->tenantId;
        }

        if (!empty($data)) {
            $options['json'] = $data;
        }

        try {
            return $this->client->request('POST', $uri, $options);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }
}
