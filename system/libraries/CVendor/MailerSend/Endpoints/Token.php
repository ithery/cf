<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_Token extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'token';

    /**
     * @param TokenParams $tokenParams
     *
     * @throws JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @return array
     */
    public function create(CVendor_MailerSend_Helpers_Builder_TokenParams $tokenParams): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($tokenParams->getName(), 1, 'Token name is required.')
                && Assertion::minLength($tokenParams->getDomainId(), 1, 'Token domain id is required.')
                && Assertion::minCount($tokenParams->getScopes(), 1, 'Token scopes are required.')
        );

        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            array_filter(
                [
                    'name' => $tokenParams->getName(),
                    'domain_id' => $tokenParams->getDomainId(),
                    'scopes' => $tokenParams->getScopes(),
                ],
            ),
        );
    }

    /**
     * @param string $id
     * @param string $status
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     *
     * @return array
     */
    public function update(string $id, string $status): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::notEmpty($id, 'Token id is required.')
                && Assertion::inArray($status, CVendor_MailerSend_Helpers_Builder_TokenParams::STATUS_ALL)
        );

        return $this->httpLayer->put(
            $this->buildUri($this->endpoint . '/' . $id . '/settings'),
            array_filter(
                [
                    'status' => $status,
                ],
            ),
        );
    }

    /**
     * @param string $id
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     *
     * @return array
     */
    public function delete(string $id): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::notEmpty($id, 'Token id is required.')
        );

        return $this->httpLayer->delete(
            $this->buildUri($this->endpoint . '/' . $id),
            []
        );
    }
}
