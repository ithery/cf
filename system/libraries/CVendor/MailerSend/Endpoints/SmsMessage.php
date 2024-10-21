<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_SmsMessage extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'sms-messages';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \JsonException
     */
    public function getAll(?int $page = null, ?int $limit = CVendor_MailerSend_Common_Constants::DEFAULT_LIMIT): array {
        if ($limit) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::range(
                    $limit,
                    CVendor_MailerSend_Common_Constants::MIN_LIMIT,
                    CVendor_MailerSend_Common_Constants::MAX_LIMIT,
                    'Limit is supposed to be between ' . CVendor_MailerSend_Common_Constants::MIN_LIMIT . ' and ' . CVendor_MailerSend_Common_Constants::MAX_LIMIT . '.'
                )
            );
        }

        return $this->httpLayer->get(
            $this->url($this->endpoint, [
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
    public function find(string $smsMessageId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsMessageId, 1, 'SMS message id is required.')
        );

        return $this->httpLayer->get(
            $this->url("$this->endpoint/$smsMessageId")
        );
    }
}
