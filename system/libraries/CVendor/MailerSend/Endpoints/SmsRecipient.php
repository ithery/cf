<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_SmsRecipient extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    public const DEFAULT_LIMIT = 25;

    public const MAX_LIMIT = 100;

    public const MIN_LIMIT = 10;

    protected string $endpoint = 'sms-recipients';

    /**
     * @param CVendor_MailerSend_Helpers_Builder_SmsRecipientParams $smsRecipientParams
     *
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @return array
     */
    public function getAll(CVendor_MailerSend_Helpers_Builder_SmsRecipientParams $smsRecipientParams): array {
        if ($limit = $smsRecipientParams->getLimit()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::min($limit, self::MIN_LIMIT, 'Minimum limit is ' . self::MIN_LIMIT . '.')
                    && Assertion::max($limit, self::MAX_LIMIT, 'Maximum limit is ' . self::MAX_LIMIT . '.')
            );
        }

        if ($smsNumberId = $smsRecipientParams->getSmsNumberId()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::minLength($smsNumberId, 1, 'SMS number id cannot be empty.')
            );
        }

        if ($status = $smsRecipientParams->getStatus()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::inArray($status, CVendor_MailerSend_Common_Constants::POSSIBLE_SMS_RECIPIENT_STATUSES),
            );
        }

        return $this->httpLayer->get($this->url($this->endpoint, $smsRecipientParams->toArray()));
    }

    /**
     * @param string $smsRecipientId
     *
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @return array
     */
    public function find(string $smsRecipientId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsRecipientId, 1, 'SMS recipient id is required.')
        );

        return $this->httpLayer->get($this->url($this->endpoint . '/' . $smsRecipientId));
    }

    /**
     * @param string $smsRecipientId
     * @param string $status
     *
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @return array
     */
    public function update(string $smsRecipientId, string $status): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsRecipientId, 1, 'SMS number id cannot be empty.')
                && Assertion::inArray($status, CVendor_MailerSend_Common_Constants::POSSIBLE_SMS_RECIPIENT_STATUSES)
        );

        return $this->httpLayer->put(
            $this->url($this->endpoint . '/' . $smsRecipientId),
            [
                'status' => $status,
            ]
        );
    }
}
