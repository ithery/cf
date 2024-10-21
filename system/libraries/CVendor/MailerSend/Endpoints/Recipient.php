<?php


use Assert\Assertion;

class CVendor_MailerSend_Endpoints_Recipient extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'recipients';

    public const DEFAULT_LIMIT = 25;
    public const MAX_LIMIT = 100;
    public const MIN_LIMIT = 10;


    /**
     * @param string|null $domainId
     * @param int|null $limit
     * @param int|null $page
     * @return array
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function get(?string $domainId, ?int $limit = self::DEFAULT_LIMIT, ?int $page = null): array
    {
        if ($limit) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::min($limit, self::MIN_LIMIT, 'Minimum limit is ' . self::MIN_LIMIT . '.') &&
                    Assertion::max($limit, self::MAX_LIMIT, 'Maximum limit is ' . self::MAX_LIMIT . '.')
            );
        }

        if ($domainId) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::minLength($domainId, 1, 'Domain id cannot be empty.')
            );
        }

        return $this->httpLayer->get(
            $this->buildUri($this->endpoint),
            array_filter([
                'domain_id' => $domainId,
                'limit' => $limit,
                'page' => $page
            ])
        );
    }

    /**
     * @param string $id
     * @return array
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function find(string $id): array
    {
        return $this->httpLayer->get($this->buildUri($this->endpoint . '/' . $id));
    }

    /**
     * @param string $id
     * @return array
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function delete(string $id): array
    {
        return $this->httpLayer->delete($this->buildUri($this->endpoint . '/' . $id));
    }
}
