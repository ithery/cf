<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_SmsInbound extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'sms-inbounds';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function getAll(?string $smsNumberId = null, ?bool $enabled = null, ?int $page = null, ?int $limit = CVendor_MailerSend_Common_Constants::DEFAULT_LIMIT): array
    {
        if ($limit) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::range(
                    $limit,
                    CVendor_MailerSend_Common_Constants::MIN_LIMIT,
                    CVendor_MailerSend_Common_Constants::MAX_LIMIT,
                    'Limit is supposed to be between ' . CVendor_MailerSend_Common_Constants::MIN_LIMIT . ' and ' . CVendor_MailerSend_Common_Constants::MAX_LIMIT .  '.'
                )
            );
        }

        return $this->httpLayer->get(
            $this->url($this->endpoint, [
                'sms_number_id' => $smsNumberId,
                'enabled' => $enabled,
                'page' => $page,
                'limit' => $limit,
            ])
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function find(string $smsInboundId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsInboundId, 1, 'SMS inbound id is required.')
        );

        return $this->httpLayer->get(
            $this->url("$this->endpoint/$smsInboundId")
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function create(CVendor_MailerSend_Helpers_Builder_SmsInbound $params): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($params->getSmsNumberId(), 1, 'SMS number id is required.') &&
                Assertion::minLength($params->getName(), 1, 'SMS inbound name is required') &&
                Assertion::url($params->getForwardUrl(), 'Invalid URL.')
        );

        return $this->httpLayer->post(
            $this->url($this->endpoint),
            array_filter($params->toArray(), function ($value) {
                return !is_null($value);
            }),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function update(string $smsInboundId, CVendor_MailerSend_Helpers_Builder_SmsInbound $params): array
    {
        return $this->httpLayer->put(
            $this->url("$this->endpoint/$smsInboundId"),
            array_filter($params->toArray(), function ($value) {
                return !is_null($value);
            }),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function delete(string $smsInboundId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsInboundId, 1, 'SMS inbound id is required.')
        );

        return $this->httpLayer->delete(
            $this->url("$this->endpoint/$smsInboundId")
        );
    }
}
