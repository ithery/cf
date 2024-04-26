<?php

class CVendor_MailerSend_Endpoints_SpamComplaint extends CVendor_MailerSend_Endpoints_Suppression {
    public function __construct(CVendor_MailerSend_Common_HttpLayer $httpLayer, array $options) {
        $endpoint = 'suppressions/spam-complaints';
        parent::__construct($httpLayer, $options, $endpoint);
    }
}
