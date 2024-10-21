<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

abstract class CVendor_BCA_ServiceAbstract {
    /**
     * Api.
     *
     * @var CVendor_BCA_Api
     */
    protected $api;

    /**
     * Token.
     *
     * @var string
     */
    private $token;

    public function __construct(CVendor_BCA_Api $api, $token = null) {
        $this->api = $api;
        $this->token = $token;
    }

    /**
     * SendRequest.
     *
     * @param string $httpMethod
     * @param string $requestUrl
     * @param array  $options
     *
     * @throws \CVendor_BCA_Exception_RequestException
     *
     * @return array
     */
    public function sendRequest(string $httpMethod, string $relativeUrl, array $requestBody = []) {
        try {
            $options = ['http_errors' => false];

            if (!$this->token) {
                $options = array_merge($options, $requestBody);
            } else {
                $url = CVendor_BCA_Helper::urlSortLexicographically("{$httpMethod}:{$relativeUrl}");
                $timestamp = CVendor_BCA_Helper::bcaTimestamp();
                ksort($requestBody);

                // set headers
                $options['headers'] = [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                    'X-BCA-Key' => $this->api->getApiKey(),
                    'X-BCA-Timestamp' => $timestamp,
                    'X-BCA-Signature' => CVendor_BCA_Helper::bcaSignature($url, $this->token, $this->api->getApiSecret(), $timestamp, $requestBody),
                ];

                $methods = ['POST', 'PUT', 'PATCH'];

                if (in_array($httpMethod, $methods)) {
                    $options['body'] = json_encode($requestBody, JSON_UNESCAPED_SLASHES);
                }
            }

            $response = c::tap(
                new CVendor_BCA_Response(
                    (new Client())->request($httpMethod, $this->api->getApiUrl() . $relativeUrl, $options)
                ),
                function ($response) {
                    if (!$response->successful()) {
                        $response->throw();
                    }
                }
            );
        } catch (ConnectException $e) {
            throw new CVendor_BCA_Exception_ConnectionException($e->getMessage(), 0, $e);
        } catch (CVendor_BCA_Exception_RequestException $e) {
            $response = $e->response;
        }

        return $this->handleResponse($response);
    }

    public function handleResponse(CVendor_BCA_Response $response) {
        $psrResponse = $response->toPsrResponse();
        $body = (string) $psrResponse->getBody();
        if ($psrResponse->getStatusCode() != 200) {
            $json = json_decode($body, false);
            if (is_array($json) && isset($json['ErrorCode'])) {
                $errorMessage = carr::get($json, 'ErrorMessage.Indonesian');

                throw new CVendor_BCA_Exception_HttpClientException($errorMessage, $psrResponse->getStatusCode());
            }

            //throw new CVendor_BCA_Exception_HttpClientException($body, $psrResponse->getStatusCode());

            throw new CVendor_BCA_Exception_HttpClientException($psrResponse->getReasonPhrase(), $psrResponse->getStatusCode());
        }
        $json = json_decode($body, true);

        return $json;
    }
}
