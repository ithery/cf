<?php

abstract class CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected CVendor_MailerSend_Common_HttpLayer $httpLayer;
    protected array $options;

    public function __construct(CVendor_MailerSend_Common_HttpLayer $httpLayer, array $options)
    {
        $this->httpLayer = $httpLayer;
        $this->options = $options;
    }

    protected function buildUri(string $path, array $params = []): string
    {
        return (new CVendor_MailerSend_Helpers_BuildUri($this->options))->execute($path, $params);
    }

    protected function url(string $path, array $params = []): string
    {
        return (new CVendor_MailerSend_Helpers_Url($this->options))->execute($path, $params);
    }
}
