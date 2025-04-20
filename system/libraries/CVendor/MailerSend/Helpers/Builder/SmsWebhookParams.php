<?php

use Assert\Assertion;
use Illuminate\Contracts\Support\Arrayable;

class CVendor_MailerSend_Helpers_Builder_SmsWebhookParams implements Arrayable, \JsonSerializable {
    public const SMS_SENT = 'sms.sent';

    public const SMS_DELIVERED = 'sms.delivered';

    public const SMS_FAILED = 'sms.failed';

    public const ALL_ACTIVITIES = [
        self::SMS_SENT,
        self::SMS_DELIVERED,
        self::SMS_FAILED,
    ];

    private ?string $url;

    private ?string $name;

    private ?array $events;

    private ?bool $enabled;

    private ?string $smsNumberId;

    /**
     * SmsWebhookParams constructor.
     *
     * @param null|string $url
     * @param null|string $name
     * @param null|array  $events
     * @param null|string $smsNumberId
     * @param null|bool   $enabled
     *
     * @throws MailerSendAssertException
     */
    public function __construct(string $url = null, string $name = null, array $events = null, string $smsNumberId = null, ?bool $enabled = null) {
        $this->setUrl($url)
            ->setName($name)
            ->setEvents($events)
            ->setEnabled($enabled)
            ->setSmsNumberId($smsNumberId);
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string {
        return $this->url;
    }

    /**
     * @param null|string $url
     *
     * @return CVendor_MailerSend_Helpers_Builder_SmsWebhookParams
     */
    public function setUrl(?string $url): CVendor_MailerSend_Helpers_Builder_SmsWebhookParams {
        $this->url = $url;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return SmsWebhookParams
     */
    public function setName(?string $name): CVendor_MailerSend_Helpers_Builder_SmsWebhookParams {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ?array
     */
    public function getEvents(): ?array {
        return $this->events;
    }

    /**
     * @param null|array $events
     *
     * @throws MailerSendAssertException
     *
     * @return $this
     */
    public function setEvents(?array $events): CVendor_MailerSend_Helpers_Builder_SmsWebhookParams {
        if ($events) {
            CVendor_MailerSend_Helpers_GeneralHelpers::assert(
                fn () => Assertion::allInArray($events, self::ALL_ACTIVITIES, 'One or multiple invalid events.')
            );
        }

        $this->events = $events;

        return $this;
    }

    /**
     * @return null|bool
     */
    public function getEnabled(): ?bool {
        return $this->enabled;
    }

    /**
     * @param null|bool $enabled
     *
     * @return CVendor_MailerSend_Helpers_Builder_SmsWebhookParams
     */
    public function setEnabled(?bool $enabled): CVendor_MailerSend_Helpers_Builder_SmsWebhookParams {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSmsNumberId(): ?string {
        return $this->smsNumberId;
    }

    /**
     * @param null|string $smsNumberId
     *
     * @return CVendor_MailerSend_Helpers_Builder_SmsWebhookParams
     */
    public function setSmsNumberId(?string $smsNumberId): CVendor_MailerSend_Helpers_Builder_SmsWebhookParams {
        $this->smsNumberId = $smsNumberId;

        return $this;
    }

    public function toArray() {
        return [
            'url' => $this->getUrl(),
            'name' => $this->getName(),
            'events' => $this->getEvents(),
            'enabled' => $this->getEnabled(),
            'sms_number_id' => $this->getSmsNumberId(),
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
