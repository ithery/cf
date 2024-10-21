<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_Inbound extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'inbound';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function getAll(?string $domainId = null, ?int $page = null, ?int $limit = CVendor_MailerSend_Common_Constants::DEFAULT_LIMIT): array {
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
            $this->buildUri($this->endpoint, [
                'domain_id' => $domainId,
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
    public function find(string $inboundId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($inboundId, 1, 'Inbound id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$inboundId")
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function create(CVendor_MailerSend_Helpers_Builder_Inbound $params): array {
        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            $params->toArray(),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function update(string $inboundId, CVendor_MailerSend_Helpers_Builder_Inbound $params): array {
        return $this->httpLayer->put(
            $this->buildUri("$this->endpoint/$inboundId"),
            $params->toArray(),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function delete(string $inboundId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($inboundId, 1, 'Inbound id is required.')
        );

        return $this->httpLayer->delete(
            $this->buildUri("$this->endpoint/$inboundId")
        );
    }
}
