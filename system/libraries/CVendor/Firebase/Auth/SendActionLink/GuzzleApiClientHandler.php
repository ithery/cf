<?php

use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Exception\GuzzleException;

final class CVendor_Firebase_Auth_SendActionLink_GuzzleApiClientHandler implements CVendor_Firebase_Auth_SendActionLink_HandlerInterface {
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
     * @param CVendor_Firebase_Auth_SendActionLink $action
     *
     * @return void
     */
    public function handle(CVendor_Firebase_Auth_SendActionLink $action) {
        $request = $this->createRequest($action);

        try {
            $response = $this->client->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {
            throw new CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException('Failed to send action link: ' . $e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException::withActionAndResponse($action, $response);
        }
    }

    /**
     * @param CVendor_Firebase_Auth_SendActionLink $action
     *
     * @return RequestInterface
     */
    private function createRequest(CVendor_Firebase_Auth_SendActionLink $action) {
        $data = \array_filter([
            'requestType' => $action->type(),
            'email' => $action->email(),
        ]) + $action->settings()->toArray();

        if ($tenantId = $action->tenantId()) {
            $uri = "https://identitytoolkit.googleapis.com/v1/projects/{$this->projectId}/tenants/{$tenantId}/accounts:sendOobCode";
            $data['tenantId'] = $tenantId;
        } else {
            $uri = "https://identitytoolkit.googleapis.com/v1/projects/{$this->projectId}/accounts:sendOobCode";
        }

        if ($idTokenString = $action->idTokenString()) {
            $data['idToken'] = $idTokenString;
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
