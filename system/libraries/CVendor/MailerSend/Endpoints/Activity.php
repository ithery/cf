<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_Activity extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'activity';

    /**
     * @throws \JsonException
     * @throws \CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getAll(string $domainId, CVendor_MailerSend_Helpers_Builder_ActivityParams $activityParams): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($domainId, 1, 'Domain id is required.')
        );

        if ($activityParams->getLimit()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::range(
                    $activityParams->getLimit(),
                    CVendor_MailerSend_Common_Constants::MIN_LIMIT,
                    CVendor_MailerSend_Common_Constants::MAX_LIMIT,
                    'Limit is supposed to be between' . CVendor_MailerSend_Common_Constants::MIN_LIMIT . ' and ' . CVendor_MailerSend_Common_Constants::MAX_LIMIT . '.'
                )
            );
        }

        if ($activityParams->getDateFrom() && $activityParams->getDateTo()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::greaterThan($activityParams->getDateTo(), $activityParams->getDateFrom())
            );
        }

        if (!empty($activityParams->getEvent())) {
            $diff = array_diff($activityParams->getEvent(), CVendor_MailerSend_Common_Constants::POSSIBLE_EVENT_TYPES);
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::count($diff, 0, 'The following types are invalid: ' . implode(', ', $diff))
            );
        }

        return $this->httpLayer->get($this->url("$this->endpoint/$domainId", $activityParams->toArray()));
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws \CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function find(string $activityId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($activityId, 1, 'Activity id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("activities/$activityId")
        );
    }
}
