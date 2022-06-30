<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class CWebhook_Server_TaskQueue_CallWebhookTask extends CQueue_AbstractTask {
    /**
     * @var null|string
     */
    public $webhookUrl = null;

    /**
     * @var string
     */
    public string $httpVerb;

    /**
     * @var int
     */
    public $tries;

    /**
     * @var int
     */
    public $requestTimeout;

    /**
     * @var string
     */
    public $backoffStrategyClass;

    /**
     * @var null|string
     */
    public ?string $signerClass = null;

    /**
     * @var array
     */
    public array $headers = [];

    /**
     * @var bool
     */
    public bool $verifySsl;

    /**
     * @var bool
     */
    public bool $throwExceptionOnFailure;

    /**
     * @var null|string
     */
    public $queue = null;

    /**
     * @var array
     */
    public array $payload = [];

    /**
     * @var array
     */
    public array $meta = [];

    /**
     * @var array
     */
    public $tags = [];

    /**
     * @var string
     */
    public $uuid = '';

    /**
     * @var null|Response
     */
    private $response = null;

    /**
     * @var null|string
     */
    private $errorType = null;

    /**
     * @var null|string
     */
    private $errorMessage = null;

    /**
     * @var null|TransferStats
     */
    private $transferStats = null;

    public function handle() {
        /** @var \GuzzleHttp\Client $client */
        $client = new Client();

        $lastAttempt = $this->attempts() >= $this->tries;

        try {
            $body = strtoupper($this->httpVerb) === 'GET'
                ? ['query' => $this->payload]
                : ['body' => json_encode($this->payload)];

            $this->response = $client->request($this->httpVerb, $this->webhookUrl, array_merge([
                'timeout' => $this->requestTimeout,
                'verify' => $this->verifySsl,
                'headers' => $this->headers,
                'on_stats' => function (TransferStats $stats) {
                    $this->transferStats = $stats;
                },
            ], $body));

            if (!cstr::startsWith($this->response->getStatusCode(), 2)) {
                throw new Exception('Webhook call failed');
            }

            $this->dispatchEvent(CWebhook_Server_Event_WebhookCallSucceededEvent::class);

            return;
        } catch (Exception $exception) {
            if ($exception instanceof RequestException) {
                $this->response = $exception->getResponse();
                $this->errorType = get_class($exception);
                $this->errorMessage = $exception->getMessage();
            }

            if ($exception instanceof ConnectException) {
                $this->errorType = get_class($exception);
                $this->errorMessage = $exception->getMessage();
            }

            if (!$lastAttempt) {
                /** @var \CWebhook_Server_BackoffStrategy_BackoffStrategy $backoffStrategy */
                $backoffStrategy = c::container()->make($this->backoffStrategyClass);

                $waitInSeconds = $backoffStrategy->waitInSecondsAfterAttempt($this->attempts());

                $this->release($waitInSeconds);
            }

            $this->dispatchEvent(CWebhook_Server_Event_WebhookCallFailedEvent::class);

            if ($lastAttempt) {
                $this->dispatchEvent(CWebhook_Server_Event_FinalWebhookCallFailedEvent::class);

                $this->throwExceptionOnFailure ? $this->fail($exception) : $this->delete();
            }
        }
    }

    /**
     * @return array
     */
    public function tags() {
        return $this->tags;
    }

    /**
     * @return null|Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @param string $eventClass
     *
     * @return void
     */
    private function dispatchEvent($eventClass) {
        c::event(new $eventClass(
            $this->httpVerb,
            $this->webhookUrl,
            $this->payload,
            $this->headers,
            $this->meta,
            $this->tags,
            $this->attempts(),
            $this->response,
            $this->errorType,
            $this->errorMessage,
            $this->uuid,
            $this->transferStats
        ));
    }
}
