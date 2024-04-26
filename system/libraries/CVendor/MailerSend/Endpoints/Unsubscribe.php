<?php

class CVendor_MailerSend_Endpoints_Unsubscribe extends CVendor_MailerSend_Endpoints_Suppression
{
    public function __construct(CVendor_MailerSend_Common_HttpLayer $httpLayer, array $options)
    {
        $endpoint = 'suppressions/unsubscribes';
        parent::__construct($httpLayer, $options, $endpoint);
    }
}
