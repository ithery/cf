<?php

use Beste\Clock\SystemClock;

final class CVendor_Firebase_JWT_CustomTokenGenerator {
    /**
     * @var CVendor_Firebase_JWT_Action_CreateCustomToken_HandlerInterface
     */
    private $handler;

    /**
     * @var null|string
     */
    private $tenantId = null;

    public function __construct(CVendor_Firebase_JWT_Action_CreateCustomToken_HandlerInterface $handler) {
        $this->handler = $handler;
    }

    /**
     * @param string $clientEmail
     * @param string $privateKey
     *
     * @return self
     */
    public static function withClientEmailAndPrivateKey($clientEmail, $privateKey) {
        $handler = new CVendor_Firebase_JWT_Action_CreateCustomToken_WithLcobucciJWT($clientEmail, $privateKey, SystemClock::create());

        return new self($handler);
    }

    public function withTenantId(string $tenantId): self {
        $generator = clone $this;
        $generator->tenantId = $tenantId;

        return $generator;
    }

    /**
     * @param CVendor_Firebase_JWT_Action_CreateCustomToken $action
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function execute(CVendor_Firebase_JWT_Action_CreateCustomToken $action) {
        if ($this->tenantId) {
            $action = $action->withTenantId($this->tenantId);
        }

        return $this->handler->handle($action);
    }

    /**
     * @param array<string, mixed>                                        $claims
     * @param CVendor_Firebase_JWT_Value_Duration|DateInterval|string|int $timeToLive
     * @param string                                                      $uid
     *
     * @throws CVendor_Firebase_JWT_Exception_CustomTokenCreationFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function createCustomToken($uid, array $claims = null, $timeToLive = null) {
        $action = CVendor_Firebase_JWT_Action_CreateCustomToken::forUid($uid);

        if ($claims !== null) {
            $action = $action->withCustomClaims($claims);
        }

        if ($timeToLive !== null) {
            $action = $action->withTimeToLive($timeToLive);
        }

        return $this->execute($action);
    }
}
