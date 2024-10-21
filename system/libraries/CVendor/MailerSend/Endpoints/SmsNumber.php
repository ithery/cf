<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_SmsNumber extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'sms-numbers';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \JsonException
     */
    public function getAll(?int $page = null, ?int $limit = CVendor_MailerSend_Common_Constants::DEFAULT_LIMIT, ?bool $paused = null): array {
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
                'paused' => $paused,
            ])
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function find(string $smsNumberId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsNumberId, 1, 'SMS number id is required.')
        );

        return $this->httpLayer->get(
            $this->url("$this->endpoint/$smsNumberId")
        );
    }

    public function update(string $smsNumberId, bool $paused): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsNumberId, 1, 'SMS number id is required.')
        );

        return $this->httpLayer->put(
            $this->url($this->endpoint . '/' . $smsNumberId),
            [
                'paused' => $paused,
            ]
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function delete(string $smsNumberId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smsNumberId, 1, 'Sms number id is required.')
        );

        return $this->httpLayer->delete(
            $this->url("$this->endpoint/$smsNumberId")
        );
    }
}
