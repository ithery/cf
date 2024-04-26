<?php

class CVendor_MailerSend_Endpoints_ApiQuota extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'api-quota';

    public function get(): array
    {
        return $this->httpLayer->get(
            $this->buildUri($this->endpoint)
        );
    }
}
