<?php


use Assert\Assertion;

class CVendor_MailerSend_Endpoints_ScheduleMessages extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'message-schedules';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function getAll(?string $domainId = null, ?string $status = null, ?int $page = null, ?int $limit = CVendor_MailerSend_Common_Constants::DEFAULT_LIMIT): array
    {
        if ($status) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::inArray(
                    $status,
                    CVendor_MailerSend_Common_Constants::SCHEDULED_MESSAGES_STATUSES,
                    'The status provided is invalid.'
                )
            );
        }

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
            $this->buildUri($this->endpoint, [
                'domain_id' => $domainId,
                'status' => $status,
                'page' => $page,
                'limit' => $limit,
            ])
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \JsonException
     */
    public function find(string $messageId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($messageId, 1, 'Message id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$messageId")
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \JsonException
     */
    public function delete(string $messageId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($messageId, 1, 'Message id is required.')
        );

        return $this->httpLayer->delete(
            $this->buildUri("$this->endpoint/$messageId"),
        );
    }
}
