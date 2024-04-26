<?php


class CVendor_MailerSend_Endpoints_HardBounce extends CVendor_MailerSend_Endpoints_Suppression
{
    public function __construct(CVendor_MailerSend_Common_HttpLayer $httpLayer, array $options)
    {
        $endpoint = 'suppressions/hard-bounces';
        parent::__construct($httpLayer, $options, $endpoint);
    }
}
