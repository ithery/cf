<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_Blocklist extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'suppressions/blocklist';

    /**
     * @throws \JsonException
     * @throws \CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
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
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function create(CVendor_MailerSend_Helpers_Builder_BlocklistParams $params): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::notEmpty(
                array_filter([$params->getRecipients(), $params->getPatterns()], fn ($v) => !empty($v)),
                'Either recipients or patterns must be provided.'
            )
            && Assertion::minLength($params->getDomainId(), 1, 'Domain id is required.')
        );

        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            $params->toArray()
        );
    }

    /**
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function delete(?array $ids = null, bool $all = false, ?string $domainId = null): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::notEmpty(
                array_filter([$ids, $all], fn ($v) => $v !== null && !empty($v)),
                'Either ids or all must be provided.'
            )
        );

        return $this->httpLayer->delete(
            $this->buildUri($this->endpoint),
            array_filter([
                'domain_id' => $domainId,
                'ids' => $ids,
                'all' => $all,
            ], fn ($e) => !is_null($e))
        );
    }
}
