<?php

use GuzzleHttp\Client;
use Beste\Clock\SystemClock;
use Psr\Cache\CacheItemPoolInterface;

final class CVendor_Firebase_JWT_IdTokenVerifier {
    /**
     * @var CVendor_Firebase_JWT_Action_VerifyIdToken_HandlerInterface
     */
    private $handler;

    /**
     * @var null|string
     */
    private $expectedTenantId = null;

    public function __construct(CVendor_Firebase_JWT_Action_VerifyIdToken_HandlerInterface $handler) {
        $this->handler = $handler;
    }

    public static function createWithProjectId(string $projectId): self {
        $clock = SystemClock::create();
        $keyHandler = new CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_WithGuzzle(new Client(['http_errors' => false]), $clock);

        $keys = new CVendor_Firebase_JWT_GooglePublicKeys($keyHandler, $clock);
        $handler = new CVendor_Firebase_JWT_Action_VerifyIdToken_WithLcobucciJWT($projectId, $keys, $clock);

        return new self($handler);
    }

    /**
     * @param string                 $projectId
     * @param CacheItemPoolInterface $cache
     *
     * @return self
     */
    public static function createWithProjectIdAndCache($projectId, CacheItemPoolInterface $cache) {
        $clock = SystemClock::create();

        $innerKeyHandler = new CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_WithGuzzle(new Client(['http_errors' => false]), $clock);
        $keyHandler = new CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_WithPsr6Cache($innerKeyHandler, $cache, $clock);

        $keys = new CVendor_Firebase_JWT_GooglePublicKeys($keyHandler, $clock);
        $handler = new CVendor_Firebase_JWT_Action_VerifyIdToken_WithLcobucciJWT($projectId, $keys, $clock);

        return new self($handler);
    }

    public function withExpectedTenantId(string $tenantId): self {
        $verifier = clone $this;
        $verifier->expectedTenantId = $tenantId;

        return $verifier;
    }

    /**
     * @param CVendor_Firebase_JWT_Action_VerifyIdToken $action
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function execute(CVendor_Firebase_JWT_Action_VerifyIdToken $action) {
        if ($this->expectedTenantId) {
            $action = $action->withExpectedTenantId($this->expectedTenantId);
        }

        return $this->handler->handle($action);
    }

    /**
     * @param string $token
     *
     * @throws CVendor_Firebase_JWT_Exception_IdTokenVerificationFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function verifyIdToken($token) {
        return $this->execute(CVendor_Firebase_JWT_Action_VerifyIdToken::withToken($token));
    }

    /**
     * @param string $token
     * @param int    $leewayInSeconds
     *
     * @throws InvalidArgumentException                                          on invalid leeway
     * @throws CVendor_Firebase_JWT_Exception_IdTokenVerificationFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function verifyIdTokenWithLeeway($token, $leewayInSeconds) {
        return $this->execute(CVendor_Firebase_JWT_Action_VerifyIdToken::withToken($token)->withLeewayInSeconds($leewayInSeconds));
    }
}
