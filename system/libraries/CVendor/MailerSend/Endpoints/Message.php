<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_Message extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    public const DEFAULT_LIMIT = 25;

    public const MAX_LIMIT = 100;

    public const MIN_LIMIT = 10;

    protected string $endpoint = 'messages';

    /**
     * @param null|int $limit
     * @param null|int $page
     *
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @return array
     */
    public function get(?int $limit = self::DEFAULT_LIMIT, ?int $page = null): array {
        if ($limit) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::min($limit, self::MIN_LIMIT, 'Minimum limit is ' . self::MIN_LIMIT . '.')
                    && Assertion::max($limit, self::MAX_LIMIT, 'Maximum limit is ' . self::MAX_LIMIT . '.')
            );
        }

        return $this->httpLayer->get(
            $this->buildUri($this->endpoint),
            array_filter([
                'limit' => $limit,
                'page' => $page
            ])
        );
    }

    /**
     * @param string $id
     *
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @return array
     */
    public function find(string $id): array {
        return $this->httpLayer->get($this->buildUri($this->endpoint . '/' . $id));
    }
}
