<?php


use Assert\Assertion;

class CVendor_MailerSend_Endpoints_Analytics extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'analytics';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \MailerSend\Exceptions\MailerSendAssertException
     * @throws \JsonException
     */
    public function activityDataByDate(CVendor_MailerSend_Helpers_Builder_ActivityAnalyticsParams $activityAnalyticsParams): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::notEmpty(
                array_filter(
                    [ $activityAnalyticsParams->getEvent()],
                    fn ($v) => $v !== null && $v !== []
                ),
                'The event[] is a required parameter.'
            )
        );

        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::greaterThan(
                $activityAnalyticsParams->getDateTo(),
                $activityAnalyticsParams->getDateFrom(),
                'The parameter date_to must be greater than date_from.'
            )
        );
        $diff = array_diff($activityAnalyticsParams->getEvent(), CVendor_MailerSend_Common_Constants::POSSIBLE_EVENT_TYPES);
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::count($diff, 0, 'The following types are invalid: ' . implode(', ', $diff))
        );

        if ($activityAnalyticsParams->getGroupBy()) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::inArray($activityAnalyticsParams->getGroupBy(), CVendor_MailerSend_Common_Constants::POSSIBLE_GROUP_BY_OPTIONS),
            );
        }

        return $this->httpLayer->get(
            $this->url("$this->endpoint/date", $activityAnalyticsParams->toArray())
        );
    }

    public function opensByCountry(CVendor_MailerSend_Helpers_Builder_OpensAnalyticsParams $opensAnalyticsParams): array
    {
        return $this->callOpensEndpoint("$this->endpoint/country", $opensAnalyticsParams);
    }

    public function opensByUserAgentName(CVendor_MailerSend_Helpers_Builder_OpensAnalyticsParams $opensAnalyticsParams): array
    {
        return $this->callOpensEndpoint("$this->endpoint/ua-name", $opensAnalyticsParams);
    }

    public function opensByReadingEnvironment(CVendor_MailerSend_Helpers_Builder_OpensAnalyticsParams $opensAnalyticsParams): array
    {
        return $this->callOpensEndpoint("$this->endpoint/ua-type", $opensAnalyticsParams);
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \MailerSend\Exceptions\MailerSendAssertException
     * @throws \JsonException
     */
    protected function callOpensEndpoint(string $path, CVendor_MailerSend_Helpers_Builder_OpensAnalyticsParams $opensAnalyticsParams): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::greaterThan(
                $opensAnalyticsParams->getDateTo(),
                $opensAnalyticsParams->getDateFrom(),
                'The parameter date_to must be greater than date_from.'
            )
        );

        return $this->httpLayer->get(
            $this->url($path, $opensAnalyticsParams->toArray())
        );
    }
}
