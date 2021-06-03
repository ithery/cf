
<?php
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

class CVendor_Xendit_HttpClient_GuzzleClient implements CVendor_Xendit_Exception_ApiException {
    private static $instance;

    protected $http;

    /**
     * XenditClient constructor
     */
    public function __construct() {
        $baseUri = strval(CVendor_Xendit_Config::$apiBase);
        $this->http = new Guzzle(
            [
                'base_uri' => $baseUri,
                'verify' => false,
                'timeout' => 60
            ]
        );
    }

    /**
     * Create Client instance
     *
     * @return GuzzleClient
     */
    public static function instance() {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Create a request to execute in _executeRequest
     *
     * @param string $method         request method
     * @param string $url            url
     * @param array  $defaultHeaders request headers
     * @param array  $params         parameters
     *
     * @return array
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    public function sendRequest($method, string $url, array $defaultHeaders, $params) {
        $method = strtoupper($method);

        $opts = [];

        $opts['method'] = $method;
        $opts['headers'] = $defaultHeaders;
        $opts['params'] = $params;

        $response = $this->executeRequest($opts, $url);

        $rbody = $response[0];
        $rcode = $response[1];
        $rheader = $response[2];

        return [$rbody, $rcode, $rheader];
    }

    /**
     * Execute request
     *
     * @param array  $opts request options (headers, params)
     * @param string $url  request url
     *
     * @return array
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    private function executeRequest(array $opts, string $url) {
        $headers = $opts['headers'];
        $params = $opts['params'];
        $apiKey = CVendor_Xendit_Config::$apiKey;
        $url = strval($url);
        try {
            if (count($params) > 0) {
                $response = $this->http->request(
                    $opts['method'],
                    $url,
                    [
                        'auth' => [$apiKey, ''],
                        'headers' => $headers,
                        RequestOptions::JSON => $params
                    ]
                );
            } else {
                $response = $this->http->request(
                    $opts['method'],
                    $url,
                    [
                        'auth' => [$apiKey, ''],
                        'headers' => $headers
                    ]
                );
            }
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $rbody = json_decode($response->getBody()->getContents(), true);
            $rcode = $response->getStatusCode();
            $rheader = $response->getHeaders();

            self::handleAPIError(
                ['body' => $rbody,
                    'code' => $rcode,
                    'header' => $rheader]
            );
        }

        $rbody = $response->getBody();
        $rcode = (int) $response->getStatusCode();
        $rheader = $response->getHeaders();

        return [$rbody, $rcode, $rheader];
    }

    /**
     * Handles API Error
     *
     * @param array $response response from GuzzleClient
     *
     * @return void
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    private static function handleAPIError($response) {
        $rbody = $response['body'];

        $rhttp = strval($response['code']);
        $message = $rbody['message'];
        $rcode = $rbody['error_code'];

        throw new CVendor_Xendit_Exception_ApiException($message, $rhttp, $rcode);
    }
}
