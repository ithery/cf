<?php

/**
 * This is the PHP SDK for MailerSend
 *
 * Class MailerSend
 *
 * @package MailerSend
 */
class CVendor_MailerSend
{
    protected array $options;
    protected static array $defaultOptions = [
        'host' => 'api.mailersend.com',
        'protocol' => 'https',
        'api_path' => 'v1',
        'api_key' => '',
        'debug' => false,
    ];

    protected ?CVendor_MailerSend_Common_HttpLayer $httpLayer;

    public CVendor_MailerSend_Endpoints_Email $email;
    public CVendor_MailerSend_Endpoints_BulkEmail $bulkEmail;
    public CVendor_MailerSend_Endpoints_Message $messages;
    public CVendor_MailerSend_Endpoints_Webhook $webhooks;
    public CVendor_MailerSend_Endpoints_Token $token;
    public CVendor_MailerSend_Endpoints_Activity $activity;
    public CVendor_MailerSend_Endpoints_Analytics $analytics;
    public CVendor_MailerSend_Endpoints_Domain $domain;
    public CVendor_MailerSend_Endpoints_Recipient $recipients;
    public CVendor_MailerSend_Endpoints_Template $template;
    public CVendor_MailerSend_Endpoints_Blocklist $blocklist;
    public CVendor_MailerSend_Endpoints_HardBounce $hardBounce;
    public CVendor_MailerSend_Endpoints_SpamComplaint $spamComplaint;
    public CVendor_MailerSend_Endpoints_Unsubscribe $unsubscribe;
    public CVendor_MailerSend_Endpoints_Inbound $inbound;
    public CVendor_MailerSend_Endpoints_ScheduleMessages $scheduleMessages;
    public CVendor_MailerSend_Endpoints_EmailVerification $emailVerification;
    public CVendor_MailerSend_Endpoints_Sms $sms;
    public CVendor_MailerSend_Endpoints_SmsNumber $smsNumber;
    public CVendor_MailerSend_Endpoints_SmsMessage $smsMessage;
    public CVendor_MailerSend_Endpoints_SmsActivity $smsActivity;
    public CVendor_MailerSend_Endpoints_SmsRecipient $smsRecipient;
    public CVendor_MailerSend_Endpoints_SmsWebhook $smsWebhook;
    public CVendor_MailerSend_Endpoints_SmsInbound $smsInbound;
    public CVendor_MailerSend_Endpoints_SenderIdentity $senderIdentity;
    public CVendor_MailerSend_Endpoints_ApiQuota $apiQuota;
    public CVendor_MailerSend_Endpoints_OnHoldList $onHoldList;
    public CVendor_MailerSend_Endpoints_SmtpUser $smtpUser;
    public CVendor_MailerSend_Endpoints_User $user;

    /**
     * @param  array  $options  Additional options for the SDK
     * @param  CVendor_MailerSend_Common_HttpLayer  $httpLayer
     * @throws CVendor_MailerSend_Exceptions_MailerSendException
     */
    public function __construct(array $options = [], ?CVendor_MailerSend_Common_HttpLayer $httpLayer = null)
    {
        $this->setOptions($options);
        $this->setHttpLayer($httpLayer);
        $this->setEndpoints();
    }

    protected function setEndpoints(): void
    {
        $this->email = new CVendor_MailerSend_Endpoints_Email($this->httpLayer, $this->options);
        $this->bulkEmail = new CVendor_MailerSend_Endpoints_BulkEmail($this->httpLayer, $this->options);
        $this->messages = new CVendor_MailerSend_Endpoints_Message($this->httpLayer, $this->options);
        $this->webhooks = new CVendor_MailerSend_Endpoints_Webhook($this->httpLayer, $this->options);
        $this->token = new CVendor_MailerSend_Endpoints_Token($this->httpLayer, $this->options);
        $this->activity = new CVendor_MailerSend_Endpoints_Activity($this->httpLayer, $this->options);
        $this->analytics = new CVendor_MailerSend_Endpoints_Analytics($this->httpLayer, $this->options);
        $this->domain = new CVendor_MailerSend_Endpoints_Domain($this->httpLayer, $this->options);
        $this->recipients = new CVendor_MailerSend_Endpoints_Recipient($this->httpLayer, $this->options);
        $this->template = new CVendor_MailerSend_Endpoints_Template($this->httpLayer, $this->options);
        $this->blocklist = new CVendor_MailerSend_Endpoints_Blocklist($this->httpLayer, $this->options);
        $this->hardBounce = new CVendor_MailerSend_Endpoints_HardBounce($this->httpLayer, $this->options);
        $this->spamComplaint = new CVendor_MailerSend_Endpoints_SpamComplaint($this->httpLayer, $this->options);
        $this->unsubscribe = new CVendor_MailerSend_Endpoints_Unsubscribe($this->httpLayer, $this->options);
        $this->inbound = new CVendor_MailerSend_Endpoints_Inbound($this->httpLayer, $this->options);
        $this->scheduleMessages = new CVendor_MailerSend_Endpoints_ScheduleMessages($this->httpLayer, $this->options);
        $this->emailVerification = new CVendor_MailerSend_Endpoints_EmailVerification($this->httpLayer, $this->options);
        $this->sms = new CVendor_MailerSend_Endpoints_Sms($this->httpLayer, $this->options);
        $this->smsNumber = new CVendor_MailerSend_Endpoints_SmsNumber($this->httpLayer, $this->options);
        $this->smsMessage = new CVendor_MailerSend_Endpoints_SmsMessage($this->httpLayer, $this->options);
        $this->smsActivity = new CVendor_MailerSend_Endpoints_SmsActivity($this->httpLayer, $this->options);
        $this->smsRecipient = new CVendor_MailerSend_Endpoints_SmsRecipient($this->httpLayer, $this->options);
        $this->smsWebhook = new CVendor_MailerSend_Endpoints_SmsWebhook($this->httpLayer, $this->options);
        $this->smsInbound = new CVendor_MailerSend_Endpoints_SmsInbound($this->httpLayer, $this->options);
        $this->senderIdentity = new CVendor_MailerSend_Endpoints_SenderIdentity($this->httpLayer, $this->options);
        $this->apiQuota = new CVendor_MailerSend_Endpoints_ApiQuota($this->httpLayer, $this->options);
        $this->onHoldList = new CVendor_MailerSend_Endpoints_OnHoldList($this->httpLayer, $this->options);
        $this->smtpUser = new CVendor_MailerSend_Endpoints_SmtpUser($this->httpLayer, $this->options);
        $this->user = new CVendor_MailerSend_Endpoints_User($this->httpLayer, $this->options);
    }

    protected function setHttpLayer(?CVendor_MailerSend_Common_HttpLayer $httpLayer = null): void
    {
        $this->httpLayer = $httpLayer ?: new CVendor_MailerSend_Common_HttpLayer($this->options);
    }

    /**
     * @throws CVendor_MailerSend_Exceptions_MailerSendException
     */
    protected function setOptions(?array $options): void
    {
        $this->options = self::$defaultOptions;

        foreach ($options as $option => $value) {
            if (array_key_exists($option, $this->options)) {
                $this->options[$option] = $value;
            }
        }

        if (!isset($this->options['api_key'])) {
            $this->options['api_key'] = getenv('MAILERSEND_API_KEY');
        }

        if (!isset($this->options['api_key'])) {
            throw new CVendor_MailerSend_Exceptions_MailerSendException('Please set "api_key" in SDK options.');
        }
    }
}
