<?php
class CWebhook_Client_WebhookProcessor {
    /**
     * @var CHTTP_Request
     */
    protected $request;

    /**
     * @var CWebhook_Client_Config
     */
    protected $config;

    public function __construct(
        $request,
        $config
    ) {
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @return CHTTP_Response
     */
    public function process() {
        $this->ensureValidSignature();

        if (!$this->config->webhookProfile->shouldProcess($this->request)) {
            return $this->createResponse();
        }

        $webhookCall = $this->storeWebhook();

        $this->processWebhook($webhookCall);

        return $this->createResponse();
    }

    protected function ensureValidSignature(): self {
        if (!$this->config->signatureValidator->isValid($this->request, $this->config)) {
            c::event(new CWebhook_Client_Event_InvalidWebhookSignatureEvent($this->request));

            throw CWebhook_Client_Exception_InvalidWebhookSignatureException::make();
        }

        return $this;
    }

    /**
     * @return WebhookCallModel
     */
    protected function storeWebhook() {
        return $this->config->webhookModel::storeWebhook($this->config, $this->request);
    }

    protected function processWebhook($webhookCall) {
        try {
            $job = new $this->config->processWebhookJobClass($webhookCall);

            $webhookCall->clearException();

            c::dispatch($job);
        } catch (Exception $exception) {
            $webhookCall->saveException($exception);

            throw $exception;
        }
    }

    /**
     * @return CHTTP_Response
     */
    protected function createResponse() {
        return $this->config->webhookResponse->respondToValidWebhook($this->request, $this->config);
    }
}
