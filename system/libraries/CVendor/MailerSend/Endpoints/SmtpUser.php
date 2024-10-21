<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_SmtpUser extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'domains';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function getAll(string $domainId = null, ?int $limit = CVendor_MailerSend_Common_Constants::DEFAULT_LIMIT): array {
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
            $this->buildUri("$this->endpoint/$domainId/smtp-users", ['limit' => $limit])
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function find(string $domainId, string $smtpUserId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($domainId, 1, 'Domain id is required.')
        );

        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smtpUserId, 1, 'Smtp user id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$domainId/smtp-users/$smtpUserId")
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function create(string $domainId, CVendor_MailerSend_Helpers_Builder_SmtpUserParams $params): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($domainId, 1, 'Domain id is required.')
        );

        return $this->httpLayer->post(
            $this->buildUri("$this->endpoint/$domainId/smtp-users"),
            $params->toArray(),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function update(string $domainId, string $smtpUserId, CVendor_MailerSend_Helpers_Builder_SmtpUserParams $params): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($domainId, 1, 'Domain id is required.')
        );

        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smtpUserId, 1, 'Smtp user id is required.')
        );

        return $this->httpLayer->put(
            $this->buildUri("$this->endpoint/$domainId/smtp-users/$smtpUserId"),
            $params->toArray(),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function delete(string $domainId, string $smtpUserId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($smtpUserId, 1, 'Smtp user id is required.')
        );

        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($domainId, 1, 'Domain id is required.')
        );

        return $this->httpLayer->delete(
            $this->buildUri("$this->endpoint/$domainId/smtp-users/$smtpUserId")
        );
    }
}
