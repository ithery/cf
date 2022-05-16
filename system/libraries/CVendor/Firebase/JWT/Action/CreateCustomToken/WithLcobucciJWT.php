<?php

use Psr\Clock\ClockInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

/**
 * @internal
 */
final class CVendor_Firebase_JWT_Action_CreateCustomToken_WithLcobucciJWT implements CVendor_Firebase_JWT_Action_CreateCustomToken_HandlerInterface {
    /**
     * @var string
     */
    private $clientEmail;

    /**
     * @var ClockInterface
     */
    private $clock;

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @param string         $clientEmail
     * @param string         $privateKey
     * @param ClockInterface $clock
     */
    public function __construct($clientEmail, $privateKey, ClockInterface $clock) {
        $this->clientEmail = $clientEmail;
        $this->clock = $clock;

        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($privateKey)
        );
    }

    /**
     * @param CVendor_Firebase_JWT_Action_CreateCustomToken $action
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function handle(CVendor_Firebase_JWT_Action_CreateCustomToken $action) {
        $now = $this->clock->now();

        $builder = $this->config->builder()
            ->issuedAt($now)
            ->issuedBy($this->clientEmail)
            ->expiresAt($now->add($action->timeToLive()->value()))
            ->relatedTo($this->clientEmail)
            ->permittedFor('https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit')
            ->withClaim('uid', $action->uid());

        if ($tenantId = $action->tenantId()) {
            $builder = $builder->withClaim('tenant_id', $tenantId);
        }

        if (!empty($customClaims = $action->customClaims())) {
            $builder = $builder->withClaim('claims', $customClaims);
        }

        try {
            $token = $builder->getToken($this->config->signer(), $this->config->signingKey());
        } catch (Throwable $e) {
            throw CVendor_Firebase_JWT_Exception_CustomTokenCreationFailedException::because($e->getMessage(), $e->getCode(), $e);
        }

        $claims = $token->claims()->all();
        foreach ($claims as &$claim) {
            if ($claim instanceof DateTimeInterface) {
                $claim = $claim->getTimestamp();
            }
        }
        unset($claim);

        $headers = $token->headers()->all();
        foreach ($headers as &$header) {
            if ($header instanceof DateTimeInterface) {
                $header = $header->getTimestamp();
            }
        }
        unset($header);

        return CVendor_Firebase_JWT_Token::withValues($token->toString(), $headers, $claims);
    }
}
