<?php

use Assert\Assertion;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class CVendor_MailerSend_Endpoints_SmsWebhook extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'sms-webhooks';

    /**
     * @param CVendor_MailerSend_Helpers_Builder_SmsWebhookParams $smsWebhookParams
     * @return array
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function create(CVendor_MailerSend_Helpers_Builder_SmsWebhookParams $smsWebhookParams): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::url($smsWebhookParams->getUrl(), 'Invalid URL.') &&
                Assertion::minLength($smsWebhookParams->getName(), 1, 'Webhook name is required.') &&
                Assertion::maxLength($smsWebhookParams->getName(), 191, 'Webhook name cannot be longer than 191 character.') &&
                Assertion::minCount($smsWebhookParams->getEvents(), 1, 'Webhook events are required.') &&
                Assertion::minLength($smsWebhookParams->getSmsNumberId(), 1, 'SMS number id is required.')
        );

        return $this->httpLayer->post(
            $this->url($this->endpoint),
            array_filter($smsWebhookParams->toArray(), function ($value) {
                return !is_null($value);
            })
        );
    }

    /**
     * @param string $smsWebhookId
     * @param string $url
     * @return array
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function update(string $smsWebhookId, CVendor_MailerSend_Helpers_Builder_SmsWebhookParams $smsWebhookParams): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsWebhookId, 1, 'SMS webhook id is required.')
        );

        return $this->httpLayer->put(
            $this->url($this->endpoint . '/' . $smsWebhookId),
            array_filter($smsWebhookParams->toArray(), function ($value) {
                return !is_null($value);
            })
        );
    }

    /**
     * @param string $smsNumberId
     * @return array
     * @throws JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function get(string $smsNumberId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsNumberId, 1, 'SMS number id is required.')
        );

        return $this->httpLayer->get(
            $this->url($this->endpoint),
            [
                'sms_number_id' => $smsNumberId
            ]
        );
    }


    /**
     * @param string $smsWebhookId
     * @return array
     * @throws JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function find(string $smsWebhookId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsWebhookId, 1, 'SMS webhook id is required.')
        );

        return $this->httpLayer->get($this->url($this->endpoint . '/' . $smsWebhookId));
    }


    /**
     * @param string $smsWebhookId
     * @return array
     * @throws JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function delete(string $smsWebhookId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsWebhookId, 1, 'SMS webhook id is required.')
        );

        return $this->httpLayer->delete($this->url($this->endpoint . '/' . $smsWebhookId));
    }
}
