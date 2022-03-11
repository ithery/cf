<?php

use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;

use GuzzleHttp\Exception\GuzzleException;

final class CVendor_Firebase_Auth_CreateActionLink_GuzzleApiClientHandler implements CVendor_Firebase_Auth_CreateActionLink_HandlerInterface {
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
    public function __construct(ClientInterface $client, $projectId) {
        $this->client = $client;
        $this->projectId = $projectId;
    }

    /**
     * @param CVendor_Firebase_Auth_CreateActionLink $action
     *
     * @return string
     */
    public function handle(CVendor_Firebase_Auth_CreateActionLink $action) {
        $request = $this->createRequest($action);

        try {
            $response = $this->client->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException('Failed to create action link: ' . $e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException::withActionAndResponse($action, $response);
        }

        try {
            $data = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);
        } catch (InvalidArgumentException $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException('Unable to parse the response data: ' . $e->getMessage(), $e->getCode(), $e);
        }

        if (!($actionCode = $data['oobLink'] ?? null)) {
            throw new CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException('The response did not contain an action link');
        }

        return (string) $actionCode;
    }

    /**
     * @param CVendor_Firebase_Auth_CreateActionLink $action
     *
     * @return RequestInterface
     */
    private function createRequest(CVendor_Firebase_Auth_CreateActionLink $action) {
        $data = \array_filter([
            'requestType' => $action->type(),
            'email' => $action->email(),
            'returnOobLink' => true,
        ]) + $action->settings()->toArray();

        if ($tenantId = $action->tenantId()) {
            $uri = "https://identitytoolkit.googleapis.com/v1/projects/{$this->projectId}/tenants/{$tenantId}/accounts:sendOobCode";
        } else {
            $uri = "https://identitytoolkit.googleapis.com/v1/projects/{$this->projectId}/accounts:sendOobCode";
        }

        $body = Utils::streamFor(CVendor_Firebase_Util_JSON::encode($data, JSON_FORCE_OBJECT));

        $headers = \array_filter([
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => (string) $body->getSize(),
            'X-Firebase-Locale' => $action->locale(),
        ]);

        return new Request('POST', $uri, $headers, $body);
    }
}
