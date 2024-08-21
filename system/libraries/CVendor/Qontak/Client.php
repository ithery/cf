<?php
use Webmozart\Assert\Assert;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Inisiatif\WhatsappQontakPhp\Message\Message;
use Http\Client\Common\HttpMethodsClientInterface;
use Psr\Http\Client\ClientInterface as HttpClient;

final class CVendor_Qontak_Client implements CVendor_Qontak_ClientInterface {
    /**
     * @var HttpMethodsClientInterface
     */
    private $httpClient;

    /**
     * @var null|string
     */
    private $accessToken = null;

    /**
     * @var CVendor_Qontak_Credential
     */
    private $credential;

    public function __construct(CVendor_Qontak_Credential $credential, HttpClient $httpClient = null) {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->httpClient = $httpClient ?? new HttpMethodsClient(
            Psr18ClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory()
        );

        $this->credential = $credential;
    }

    public function getContactList($channelIntegrationId, $phoneNumbers = []) {
        $this->getAccessToken();

        $response = $this->httpClient->post(
            'https://service-chat.qontak.com/api/open/v1/contacts/contact_list',
            [
                'content-type' => 'application/json',
                'Authorization' => \sprintf('Bearer %s', $this->accessToken ?? ''),
            ],
            \json_encode([
                'channel_integration_id' => $channelIntegrationId,
                'phone_numbers' => $phoneNumbers,
            ])
        );

        cdbg::dd($response);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            /** @var array $responseBody */
            $responseBody = \json_decode((string) $response->getBody(), true);

            Assert::keyExists($responseBody, 'data');

            /** @var array<string, string|int> $responseData */
            $responseData = $responseBody['data'];
            Assert::keyExists($responseData, 'id');
            Assert::keyExists($responseData, 'name');

            return new CVendor_Qontak_Response((string) $responseData['id'], (string) $responseData['name'], $responseData);
        }

        throw CVendor_Qontak_Exception_ClientSendingException::make($response);
    }

    public function send(string $templateId, string $channelId, CVendor_Qontak_Message $message): CVendor_Qontak_Response {
        $this->getAccessToken();

        $response = $this->httpClient->post(
            'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
            [
                'content-type' => 'application/json',
                'Authorization' => \sprintf('Bearer %s', $this->accessToken ?? ''),
            ],
            \json_encode(
                [
                    'message_template_id' => $templateId,
                    'channel_integration_id' => $channelId,
                ] + $this->makeRequestBody($message)
            )
        );

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            /** @var array $responseBody */
            $responseBody = \json_decode((string) $response->getBody(), true);

            Assert::keyExists($responseBody, 'data');

            /** @var array<string, string|int> $responseData */
            $responseData = $responseBody['data'];
            Assert::keyExists($responseData, 'id');
            Assert::keyExists($responseData, 'name');

            return new CVendor_Qontak_Response((string) $responseData['id'], (string) $responseData['name'], $responseData);
        }

        throw CVendor_Qontak_Exception_ClientSendingException::make($response);
    }

    private function getAccessToken(): void {

        // cdbg::dd($this->credential->getOAuthCredential());
        if ($this->accessToken === null) {
            $response = $this->httpClient->post(
                'https://service-chat.qontak.com/oauth/token',
                [
                    'content-type' => 'application/json',
                ],
                \json_encode($this->credential->getOAuthCredential())
            );

            /** @var array<array-key, string> $body */
            $body = \json_decode((string) $response->getBody(), true);
            cdbg::dd($body);
            Assert::keyExists($body, 'access_token');

            $this->accessToken = $body['access_token'];
        }
    }

    private function makeRequestBody(CVendor_Qontak_Message $message): array {
        return CVendor_Qontak_MessageUtil::makeRequestBody($message);
    }
}
