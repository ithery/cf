<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2019, 7:07:06 AM
 */
use Monolog\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\MessageFormatter;
use Monolog\Handler\RotatingFileHandler;
use GuzzleHttp\Exception\RequestException;

class CVendor_Watzap_Adapter_GuzzleAdapter implements CVendor_Watzap_Contract_AdapterInterface {
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $apiKey;

    protected $isLog;

    /**
     * @param mixed $options
     */
    public function __construct(array $options = []) {
        $this->isLog = carr::get($options, 'logging', false);
        $this->apiKey = carr::get($options, 'apiKey');
        $options = [];

        if ($this->isLog) {
            $messageFormats = [
                'REQUEST: {method} - {uri} - HTTP/{version} - {req_headers} - {req_body}',
                'RESPONSE: {code} - {res_body}',
            ];

            $stack = HandlerStack::create();

            c::collect($messageFormats)->each(function ($messageFormat) use ($stack) {
                // We'll use unshift instead of push, to add the middleware to the bottom of the stack, not the top
                $stack->unshift(
                    Middleware::log(
                        c::with(new Logger('guzzle-log'))->pushHandler(
                            new RotatingFileHandler(DOCROOT . 'temp/logs/vendor/' . CF::appCode() . '/watzap/guzzle-log.log')
                        ),
                        new MessageFormatter($messageFormat)
                    )
                );
            });
            $options['handler'] = $stack;
        }
        $client = new Client($options);

        $this->client = $client;
    }

    protected function getDefaultOptions() {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ]
        ];

        return $options;
    }

    /**
     * @inheritdoc
     */
    public function post($url, $content = '', $headers = null) {
        $options = $this->getDefaultOptions();
        $content['api_key'] = $this->apiKey;
        if ($this->numberKey) {
            $options['json'] = $content;
        }

        try {
            $this->response = $this->client->post($url, $options);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();
            $this->handleError($e);
        }

        return (string) $this->response->getBody();
    }

    /**
     * @param mixed $e
     *
     * @throws CVendor_Watzap_Exception_HttpException
     * @throws CVendor_Watzap_Exception_ApiException
     */
    protected function handleError($e) {
        if ($this->response == null) {
            throw $e;
        }
        $body = (string) $this->response->getBody();
        $code = (int) $this->response->getStatusCode();
        if ($code != 200) {
            throw new CVendor_Watzap_Exception_HttpException(isset($body) ? $body : 'Request not processed.', $code);
        }
        $content = json_decode($body);

        throw new CVendor_Watzap_Exception_ApiException(isset($content->message) ? $content->message : 'Request not processed.', $code);
    }
}
