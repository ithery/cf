<?php

declare(strict_types=1);

namespace Kreait\Firebase;

/**
 * @internal
 */
final class ServiceAccount
{
    public string $type;

    public string $projectId;

    public string $clientEmail;

    public string $privateKey;

    public ?string $clientId = null;

    public ?string $privateKeyId = null;

    public ?string $authUri = null;

    public ?string $tokenUri = null;

    public ?string $authProviderX509CertUrl = null;

    public ?string $clientX509CertUrl = null;

    public ?string $quotaProjectId = null;

    public ?string $universeDomain = null;
    public function __construct(
        /** @var non-empty-string */
        string $type,
        /** @var non-empty-string */
        #[\SensitiveParameter]
        string $projectId,
        /** @var non-empty-string */
        #[\SensitiveParameter]
        string $clientEmail,
        /** @var non-empty-string */
        #[\SensitiveParameter]
        string $privateKey,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        ?string $clientId = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        ?string $privateKeyId = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        ?string $authUri = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        ?string $tokenUri = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        ?string $authProviderX509CertUrl = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        ?string $clientX509CertUrl = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        ?string $quotaProjectId = null,
        /** @var non-empty-string|null */
        #[\SensitiveParameter]
        ?string $universeDomain = null
    ) {
        $this->type = $type;
        $this->projectId = $projectId;
        $this->clientEmail = $clientEmail;
        $this->privateKey = $privateKey;
        $this->clientId = $clientId;
        $this->privateKeyId = $privateKeyId;
        $this->authUri = $authUri;
        $this->tokenUri = $tokenUri;
        $this->authProviderX509CertUrl = $authProviderX509CertUrl;
        $this->clientX509CertUrl = $clientX509CertUrl;
        $this->quotaProjectId = $quotaProjectId;
        $this->universeDomain = $universeDomain;
    }
}
