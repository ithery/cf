<?php

use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Exception\GuzzleException;

final class CVendor_Firebase_Auth_CreateSessionCookie_GuzzleApiClientHandler implements CVendor_Firebase_Auth_CreateSessionCookie_HandlerInterface {
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @param ClientInterface $client
     * @param string          $projectId
     */
    public function __construct(ClientInterface $client, string $projectId) {
        $this->client = $client;
        $this->projectId = $projectId;
    }

    /**
     * @param CVendor_Firebase_Auth_CreateSessionCookie $action
     *
     * @return string
     */
    public function handle(CVendor_Firebase_Auth_CreateSessionCookie $action) {
        $request = $this->createRequest($action);

        try {
            $response = $this->client->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToCreateSessionCookieException($action, null, 'Connection error', 0, $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw CVendor_Firebase_Auth_Exception_FailedToCreateSessionCookieException::withActionAndResponse($action, $response);
        }

        try {
            /** @var array{sessionCookie?: string|null} $data */
            $data = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);
        } catch (\InvalidArgumentException $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToCreateSessionCookieException($action, $response, 'Unable to parse the response data: ' . $e->getMessage(), 0, $e);
        }

        $sessionCookie = $data['sessionCookie'] ?? null;

        if ($sessionCookie !== null) {
            return $sessionCookie;
        }

        throw new CVendor_Firebase_Auth_Exception_FailedToCreateSessionCookieException($action, $response, 'The response did not contain a session cookie');
    }

    /**
     * @param CVendor_Firebase_Auth_CreateSessionCookie $action
     *
     * @return RequestInterface
     */
    private function createRequest(CVendor_Firebase_Auth_CreateSessionCookie $action) {
        $data = [
            'idToken' => $action->idToken(),
            'validDuration' => $action->ttlInSeconds(),
        ];

        if ($tenantId = $action->tenantId()) {
            $uri = "https://identitytoolkit.googleapis.com/v1/projects/{$this->projectId}/tenants/{$tenantId}:createSessionCookie";
        } else {
            $uri = "https://identitytoolkit.googleapis.com/v1/projects/{$this->projectId}:createSessionCookie";
        }

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode($data, JSON_FORCE_OBJECT));

        $headers = \array_filter([
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => (string) $body->getSize(),
        ]);

        return new Request('POST', $uri, $headers, $body);
    }
}
