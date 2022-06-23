<?php

use Spatie\WebhookServer\Signer\Signer;
use Spatie\WebhookServer\Exceptions\InvalidSigner;
use Spatie\WebhookServer\Exceptions\CouldNotCallWebhook;
use Spatie\WebhookServer\BackoffStrategy\BackoffStrategy;
use Spatie\WebhookServer\Exceptions\InvalidBackoffStrategy;

class CWebhook_Server_WebhookCall {
    protected CWebhook_Server_TaskQueue_CallWebhookTask $callWebhookJob;

    /**
     * @var string
     */
    protected $uuid = '';

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var CWebhook_Server_Contract_SignerInterface
     */
    protected $signer;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    private $payload = [];

    /**
     * @var bool
     */
    private $signWebhook = true;

    /**
     * @param mixed $name
     *
     * @return self
     */
    public static function create($name = 'default') {
        $config = CF::config('webhook.server.' . $name);

        return (new static())
            ->uuid(cstr::uuid())
            ->onQueue($config['queue'])
            ->onConnection($config['connection'] ?? null)
            ->useHttpVerb($config['http_verb'])
            ->maximumTries($config['tries'])
            ->useBackoffStrategy($config['backoff_strategy'])
            ->timeoutInSeconds($config['timeout_in_seconds'])
            ->signUsing($config['signer'])
            ->withHeaders($config['headers'])
            ->withTags($config['tags'])
            ->verifySsl($config['verify_ssl'])
            ->throwExceptionOnFailure($config['throw_exception_on_failure']);
    }

    public function __construct() {
        $this->callWebhookJob = c::container()->make(CWebhook_Server_TaskQueue_CallWebhookTask::class);
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function url($url) {
        $this->callWebhookJob->webhookUrl = $url;

        return $this;
    }

    /**
     * @param array $payload
     *
     * @return self
     */
    public function payload(array $payload) {
        $this->payload = $payload;

        $this->callWebhookJob->payload = $payload;

        return $this;
    }

    /**
     * @param string $uuid
     *
     * @return self
     */
    public function uuid($uuid) {
        $this->uuid = $uuid;

        $this->callWebhookJob->uuid = $uuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid() {
        return $this->uuid;
    }

    /**
     * @param null|string $queue
     *
     * @return self
     */
    public function onQueue($queue) {
        $this->callWebhookJob->queue = $queue;

        return $this;
    }

    /**
     * @param null|string $connection
     *
     * @return self
     */
    public function onConnection($connection) {
        $this->callWebhookJob->connection = $connection;

        return $this;
    }

    /**
     * @param string $secret
     *
     * @return self
     */
    public function useSecret($secret) {
        $this->secret = $secret;

        return $this;
    }

    /**
     * @param string $verb
     *
     * @return self
     */
    public function useHttpVerb($verb) {
        $this->callWebhookJob->httpVerb = $verb;

        return $this;
    }

    /**
     * @param int $tries
     *
     * @return self
     */
    public function maximumTries($tries) {
        $this->callWebhookJob->tries = $tries;

        return $this;
    }

    /**
     * @param string $backoffStrategyClass
     *
     * @return self
     */
    public function useBackoffStrategy($backoffStrategyClass) {
        if (!is_subclass_of($backoffStrategyClass, CWebhook_Server_Contract_BackoffStrategyInterface::class)) {
            throw CWebhook_Server_Exception_InvalidBackoffStrategyException::doesNotExtendBackoffStrategy($backoffStrategyClass);
        }

        $this->callWebhookJob->backoffStrategyClass = $backoffStrategyClass;

        return $this;
    }

    /**
     * @param int $timeoutInSeconds
     *
     * @return self
     */
    public function timeoutInSeconds($timeoutInSeconds) {
        $this->callWebhookJob->requestTimeout = $timeoutInSeconds;

        return $this;
    }

    /**
     * @param string $signerClass
     *
     * @return self
     */
    public function signUsing($signerClass) {
        if (!is_subclass_of($signerClass, CWebhook_Server_Contract_SignerInterface::class)) {
            throw CWebhook_Server_Exception_InvalidSignerException::doesNotImplementSigner($signerClass);
        }

        $this->signer = c::container()->make($signerClass);

        return $this;
    }

    /**
     * @return self
     */
    public function doNotSign() {
        $this->signWebhook = false;

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return self
     */
    public function withHeaders(array $headers) {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * @param bool $verifySsl
     *
     * @return self
     */
    public function verifySsl(bool $verifySsl = true) {
        $this->callWebhookJob->verifySsl = $verifySsl;

        return $this;
    }

    /**
     * @return self
     */
    public function doNotVerifySsl() {
        $this->verifySsl(false);

        return $this;
    }

    /**
     * @param bool $throwExceptionOnFailure
     *
     * @return self
     */
    public function throwExceptionOnFailure($throwExceptionOnFailure = true) {
        $this->callWebhookJob->throwExceptionOnFailure = $throwExceptionOnFailure;

        return $this;
    }

    /**
     * @param array $meta
     *
     * @return self
     */
    public function meta(array $meta) {
        $this->callWebhookJob->meta = $meta;

        return $this;
    }

    /**
     * @param array $tags
     *
     * @return self
     */
    public function withTags(array $tags) {
        $this->callWebhookJob->tags = $tags;

        return $this;
    }

    /**
     * @return CQueue_PendingDispatch
     */
    public function dispatch() {
        $this->prepareForDispatch();

        return c::dispatch($this->callWebhookJob);
    }

    /**
     * @return void
     */
    public function dispatchSync() {
        $this->prepareForDispatch();

        c::dispatchSync($this->callWebhookJob);
    }

    /**
     * @return void
     */
    protected function prepareForDispatch() {
        if (!$this->callWebhookJob->webhookUrl) {
            throw CWebhook_Server_Exception_CouldNotCallWebhookException::urlNotSet();
        }

        if ($this->signWebhook && empty($this->secret)) {
            throw CWebhook_Server_Exception_CouldNotCallWebhookException::secretNotSet();
        }

        $this->callWebhookJob->headers = $this->getAllHeaders();
    }

    /**
     * @return array
     */
    protected function getAllHeaders() {
        $headers = $this->headers;

        if (!$this->signWebhook) {
            return $headers;
        }

        $signature = $this->signer->calculateSignature($this->callWebhookJob->webhookUrl, $this->payload, $this->secret);

        $headers[$this->signer->signatureHeaderName()] = $signature;

        return $headers;
    }
}
