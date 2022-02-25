<?php

final class CVendor_Firebase_JWT_Action_CreateCustomToken {
    public const MINIMUM_TTL = 'PT1S';

    public const MAXIMUM_TTL = 'PT1H';

    public const DEFAULT_TTL = self::MAXIMUM_TTL;

    /**
     * @var string
     */
    private $uid;

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @var array<string, mixed>
     */
    private $customClaims = [];

    /**
     * @var CVendor_Firebase_JWT_Value_Duration
     */
    private $ttl;

    /**
     * @param string $uid
     */
    private function __construct($uid) {
        $this->uid = $uid;
        $this->ttl = CVendor_Firebase_JWT_Value_Duration::fromDateIntervalSpec(self::DEFAULT_TTL);
    }

    /**
     * @param string $uid
     *
     * @return self
     */
    public static function forUid($uid) {
        return new self($uid);
    }

    /**
     * @param string $tenantId
     *
     * @return self
     */
    public function withTenantId($tenantId) {
        $action = clone $this;
        $action->tenantId = $tenantId;

        return $action;
    }

    /**
     * @param string $uid
     *
     * @return self
     */
    public function withChangedUid($uid) {
        $action = clone $this;
        $action->uid = $uid;

        return $action;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return self
     */
    public function withCustomClaim($name, $value) {
        $action = clone $this;
        $action->customClaims[$name] = $value;

        return $action;
    }

    /**
     * @param array<string, mixed> $claims
     *
     * @return self
     */
    public function withCustomClaims(array $claims) {
        $action = clone $this;
        $action->customClaims = $claims;

        return $action;
    }

    /**
     * @param array<string, mixed> $claims
     *
     * @return self
     */
    public function withAddedCustomClaims(array $claims) {
        $action = clone $this;
        $action->customClaims = \array_merge($action->customClaims, $claims);

        return $action;
    }

    /**
     * @param CVendor_Firebase_JWT_Value_Duration|DateInterval|string|int $ttl
     *
     * @return self
     */
    public function withTimeToLive($ttl) {
        $ttl = CVendor_Firebase_JWT_Value_Duration::make($ttl);

        $minTtl = CVendor_Firebase_JWT_Value_Duration::fromDateIntervalSpec(self::MINIMUM_TTL);
        $maxTtl = CVendor_Firebase_JWT_Value_Duration::fromDateIntervalSpec(self::MAXIMUM_TTL);

        if ($ttl->isSmallerThan($minTtl) || $ttl->isLargerThan($maxTtl)) {
            $message = 'The expiration time of a custom token must be between %s and %s, but got %s';

            throw new InvalidArgumentException(\sprintf($message, $minTtl, $maxTtl, $ttl));
        }

        $action = clone $this;
        $action->ttl = $ttl;

        return $action;
    }

    /**
     * @return string
     */
    public function uid() {
        return $this->uid;
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->tenantId;
    }

    /**
     * @return array<string, mixed>
     */
    public function customClaims() {
        return $this->customClaims;
    }

    /**
     * @return CVendor_Firebase_JWT_Value_Duration
     */
    public function timeToLive() {
        return $this->ttl;
    }
}
