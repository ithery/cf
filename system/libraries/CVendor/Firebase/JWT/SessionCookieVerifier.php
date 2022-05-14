<?php

use GuzzleHttp\Client;
use Beste\Clock\SystemClock;
use Psr\Cache\CacheItemPoolInterface;
use Kreait\Firebase\JWT\Action\VerifySessionCookie;

final class CVendor_Firebase_JWT_SessionCookieVerifier {
    /**
     * @var CVendor_Firebase_JWT_Action_VerifySessionCookie_HandlerInterface
     */
    private $handler;

    /**
     * @var null|string
     */
    private $expectedTenantId = null;

    /**
     * @param CVendor_Firebase_JWT_Action_VerifySessionCookie_HandlerInterface $handler
     */
    public function __construct(CVendor_Firebase_JWT_Action_VerifySessionCookie_HandlerInterface $handler) {
        $this->handler = $handler;
    }

    /**
     * @param string $projectId
     *
     * @return self
     */
    public static function createWithProjectId($projectId) {
        $clock = SystemClock::create();
        $keyHandler = new CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_WithGuzzle(new Client(['http_errors' => false]), $clock);

        $keys = new CVendor_Firebase_JWT_GooglePublicKeys($keyHandler, $clock);
        $handler = new CVendor_Firebase_JWT_Action_VerifySessionCookie_WithLcobucciJWT($projectId, $keys, $clock);

        return new self($handler);
    }

    public static function createWithProjectIdAndCache(string $projectId, CacheItemPoolInterface $cache): self {
        $clock = SystemClock::create();

        $innerKeyHandler = new CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_WithGuzzle(new Client(['http_errors' => false]), $clock);
        $keyHandler = new CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_WithPsr6Cache($innerKeyHandler, $cache, $clock);

        $keys = new CVendor_Firebase_JWT_GooglePublicKeys($keyHandler, $clock);
        $handler = new CVendor_Firebase_JWT_Action_VerifySessionCookie_WithLcobucciJWT($projectId, $keys, $clock);

        return new self($handler);
    }

    /**
     * @param string $tenantId
     *
     * @return self
     */
    public function withExpectedTenantId($tenantId) {
        $generator = clone $this;
        $generator->expectedTenantId = $tenantId;

        return $generator;
    }

    /**
     * Undocumented function.
     *
     * @param CVendor_Firebase_JWT_Action_VerifySessionCookie $action
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function execute(CVendor_Firebase_JWT_Action_VerifySessionCookie $action) {
        if ($this->expectedTenantId) {
            $action = $action->withExpectedTenantId($this->expectedTenantId);
        }

        return $this->handler->handle($action);
    }

    /**
     * @param string $sessionCookie
     *
     * @throws CVendor_Firebase_JWT_Exception_SessionCookieVerificationFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function verifySessionCookie($sessionCookie) {
        return $this->execute(CVendor_Firebase_JWT_Action_VerifySessionCookie::withSessionCookie($sessionCookie));
    }

    /**
     * @param string $sessionCookie
     * @param int    $leewayInSeconds
     *
     * @throws InvalidArgumentException                                                on invalid leeway
     * @throws CVendor_Firebase_JWT_Exception_SessionCookieVerificationFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function verifySessionCookieWithLeeway($sessionCookie, $leewayInSeconds) {
        return $this->execute(CVendor_Firebase_JWT_Action_VerifySessionCookie::withSessionCookie($sessionCookie)->withLeewayInSeconds($leewayInSeconds));
    }
}
