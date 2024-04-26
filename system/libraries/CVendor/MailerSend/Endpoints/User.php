<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_User extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'users';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function getAll(): array
    {
        return $this->httpLayer->get(
            $this->buildUri($this->endpoint)
        );
    }


    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function find(string $userId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($userId, 1, 'User id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$userId")
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function create(CVendor_MailerSend_Helpers_Builder_UserParams $params): array
    {
        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            $params->toArray(),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function update(string $userId, CVendor_MailerSend_Helpers_Builder_UserParams $params): array
    {
        return $this->httpLayer->put(
            $this->buildUri("$this->endpoint/$userId"),
            $params->toArray(),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function delete(string $userId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($userId, 1, 'User id is required.')
        );

        return $this->httpLayer->delete(
            $this->buildUri("$this->endpoint/$userId")
        );
    }
}
