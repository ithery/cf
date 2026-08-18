<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_SmsActivity extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'sms-activity';

    /**
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getAll(CVendor_MailerSend_Helpers_Builder_SmsActivityParams $smsActivityParams): array {
        if ($smsActivityParams->getSmsNumberId()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::minLength($smsActivityParams->getSmsNumberId(), 1, 'Sms number id is wrong.')
            );
        }

        if ($smsActivityParams->getLimit()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::range(
                    $smsActivityParams->getLimit(),
                    CVendor_MailerSend_Common_Constants::MIN_LIMIT,
                    CVendor_MailerSend_Common_Constants::MAX_LIMIT,
                    'Limit is supposed to be between' . CVendor_MailerSend_Common_Constants::MIN_LIMIT . ' and ' . CVendor_MailerSend_Common_Constants::MAX_LIMIT . '.'
                )
            );
        }

        if ($smsActivityParams->getDateFrom() && $smsActivityParams->getDateTo()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::greaterThan($smsActivityParams->getDateTo(), $smsActivityParams->getDateFrom())
            );
        }

        if (!empty($smsActivityParams->getStatus())) {
            $diff = array_diff($smsActivityParams->getStatus(), CVendor_MailerSend_Common_Constants::POSSIBLE_SMS_STATUSES);
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::count($diff, 0, 'The following statuses are invalid: ' . implode(', ', $diff))
            );
        }

        return $this->httpLayer->get($this->url("$this->endpoint", $smsActivityParams->toArray()));
    }
}
