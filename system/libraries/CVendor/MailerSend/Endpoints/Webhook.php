<?php

use Assert\Assertion;

class CVendor_MailerSend_Endpoints_Webhook extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'webhooks';

    /**
     * @param WebhookParams $webhookParams
     *
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     *
     * @return array
     */
    public function create(CVendor_MailerSend_Helpers_Builder_WebhookParams $webhookParams): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::url($webhookParams->getUrl(), 'Invalid URL.')
                && Assertion::minLength($webhookParams->getName(), 1, 'Webhook name is required.')
                && Assertion::maxLength($webhookParams->getName(), 191, 'Webhook name cannot be longer than 191 character.')
                && Assertion::minCount($webhookParams->getEvents(), 1, 'Webhook events are required.')
                && Assertion::minLength($webhookParams->getDomainId(), 1, 'Webhook domain id is required.')
        );

        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            array_filter($webhookParams->toArray())
        );
    }

    /**
     * @param string    $id
     * @param string    $url
     * @param string    $name
     * @param array     $events
     * @param null|bool $enabled
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     *
     * @return array
     */
    public function update(string $id, string $url, string $name, array $events, ?bool $enabled = null): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($id, 1, 'Webhook id is required.')
                && Assertion::url($url, 'Invalid URL.')
                && Assertion::minLength($name, 1, 'Webhook name is required.')
                && Assertion::minCount($events, 1, 'Webhook events are required.')
                && Assertion::allInArray($events, CVendor_MailerSend_Helpers_Builder_WebhookParams::ALL_ACTIVITIES, 'One or multiple invalid events.')
        );

        return $this->httpLayer->put(
            $this->buildUri($this->endpoint . '/' . $id),
            array_filter([
                'url' => $url,
                'name' => $name,
                'events' => $events,
                'enabled' => $enabled,
            ])
        );
    }

    /**
     * @param string $domainId
     *
     * @throws JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @return array
     */
    public function get(string $domainId): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($domainId, 1, 'Domain id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri($this->endpoint),
            array_filter([
                'domain_id' => $domainId
            ])
        );
    }

    /**
     * @param string $id
     *
     * @throws JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     *
     * @return array
     */
    public function find(string $id): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($id, 1, 'Webhook id is required.')
        );

        return $this->httpLayer->get($this->buildUri($this->endpoint . '/' . $id));
    }

    /**
     * @param string $id
     *
     * @throws JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     *
     * @return array
     */
    public function delete(string $id): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($id, 1, 'Webhook id is required.')
        );

        return $this->httpLayer->delete($this->buildUri($this->endpoint . '/' . $id));
    }
}
