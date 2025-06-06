<?php

use Assert\Assertion;
use Illuminate\Contracts\Support\Arrayable;

class CVendor_MailerSend_Helpers_Builder_TokenParams implements Arrayable, JsonSerializable {
    public const EMAIL_FULL = 'email_full';

    public const DOMAINS_READ = 'domains_read';

    public const DOMAINS_FULL = 'domains_full';

    public const ACTIVITY_READ = 'activity_read';

    public const ACTIVITY_FULL = 'activity_full';

    public const ANALYTICS_READ = 'analytics_read';

    public const ANALYTICS_FULL = 'analytics_full';

    public const TOKENS_FULL = 'tokens_full';

    public const WEBHOOKS_FULL = 'webhooks_full';

    public const TEMPLATES_FULL = 'templates_full';

    public const ALL_SCOPES = [
        self::EMAIL_FULL,
        self::DOMAINS_READ, self::DOMAINS_FULL,
        self::ACTIVITY_READ, self::ACTIVITY_FULL,
        self::ANALYTICS_READ, self::ANALYTICS_FULL,
        self::TOKENS_FULL,
        self::WEBHOOKS_FULL,
        self::TEMPLATES_FULL,
    ];

    public const STATUS_PAUSE = 'pause';

    public const STATUS_UNPAUSE = 'unpause';

    public const STATUS_ALL = [self::STATUS_PAUSE, self::STATUS_UNPAUSE];

    private string $name;

    private string $domainId;

    private array $scopes;

    /**
     * TokenParams constructor.
     *
     * @param string $name
     * @param string $domainId
     * @param array  $scopes
     *
     * @throws MailerSendAssertException
     */
    public function __construct(string $name, string $domainId, array $scopes) {
        $this->setName($name)
            ->setDomainId($domainId)
            ->setScopes($scopes);
    }

    /**
     * @return null|string
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return CVendor_MailerSend_Helpers_Builder_TokenParams
     */
    public function setName(string $name): CVendor_MailerSend_Helpers_Builder_TokenParams {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomainId(): string {
        return $this->domainId;
    }

    /**
     * @param string $domainId
     *
     * @return CVendor_MailerSend_Helpers_Builder_TokenParams
     */
    public function setDomainId(string $domainId): CVendor_MailerSend_Helpers_Builder_TokenParams {
        $this->domainId = $domainId;

        return $this;
    }

    /**
     * @return array
     */
    public function getScopes(): array {
        return $this->scopes;
    }

    /**
     * @param array $scopes
     *
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     *
     * @return CVendor_MailerSend_Helpers_Builder_TokenParams
     */
    public function setScopes(array $scopes): CVendor_MailerSend_Helpers_Builder_TokenParams {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::allInArray($scopes, self::ALL_SCOPES, 'Some scopes are not valid.')
        );

        $this->scopes = $scopes;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return array_filter([
            'name' => $this->getName(),
            'domain_id' => $this->getDomainId(),
            'scopes' => $this->getScopes(),
        ]);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
