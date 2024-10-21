<?php


use Assert\Assertion;

class CVendor_MailerSend_Endpoints_EmailVerification extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'email-verification';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \JsonException
     */
    public function getAll(?int $page = null, ?int $limit = CVendor_MailerSend_Common_Constants::DEFAULT_LIMIT): array
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
            $this->buildUri($this->endpoint, [
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
    public function find(string $emailVerificationId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($emailVerificationId, 1, 'Email Verification id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$emailVerificationId")
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function create(CVendor_MailerSend_Helpers_Builder_EmailVerificationParams $params): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($params->getName(), 1, 'Email Verification name is required.')
        );

        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            $params->toArray()
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \JsonException
     */
    public function verify(string $emailVerificationId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($emailVerificationId, 1, 'Email Verification id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$emailVerificationId/verify")
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \JsonException
     */
    public function getResults(
        string $emailVerificationId,
        ?int $page = null,
        ?int $limit = CVendor_MailerSend_Common_Constants::DEFAULT_LIMIT,
        array $results = []
    ): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($emailVerificationId, 1, 'Email Verification id is required.')
        );

        if (!empty($results)) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::allInArray($results, CVendor_MailerSend_Helpers_Builder_EmailVerificationParams::POSSIBLE_RESULTS)
            );
        }

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$emailVerificationId/results", [
                'page' => $page,
                'limit' => $limit,
                'results' => $results,
            ])
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function verifyEmail(string $email): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($email, 1, 'Email address is required.')
        );

        return $this->httpLayer->post(
            $this->buildUri("{$this->endpoint}/verify"),
            ['email' => $email]
        );
    }
}
