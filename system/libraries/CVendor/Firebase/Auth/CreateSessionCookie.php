<?php

use Lcobucci\JWT\Token;
use Beste\Clock\SystemClock;
use Psr\Clock\ClockInterface;

final class CVendor_Firebase_Auth_CreateSessionCookie {
    private const FIVE_MINUTES = 'PT5M';

    private const TWO_WEEKS = 'P14D';

    /**
     * @var string
     */
    private $idToken;

    /**
     * @var null|string
     */
    private $tenantId;

    /**
     * @var DateInterval
     */
    private $ttl;

    /**
     * @var ClockInterface
     */
    private $clock;

    /**
     * @param string         $idToken
     * @param null|string    $tenantId
     * @param DateInterval   $ttl
     * @param ClockInterface $clock
     */
    private function __construct($idToken, $tenantId, DateInterval $ttl, ClockInterface $clock) {
        $this->idToken = $idToken;
        $this->tenantId = $tenantId;
        $this->ttl = $ttl;
        $this->clock = $clock;
    }

    /**
     * @param Token|string     $idToken
     * @param int|DateInterval $ttl
     * @param null|string      $tenantId
     *
     * @return self
     */
    public static function forIdToken($idToken, $tenantId, $ttl, ClockInterface $clock = null) {
        $clock ??= SystemClock::create();

        if ($idToken instanceof Token) {
            $idToken = $idToken->toString();
        }

        $ttl = self::assertValidDuration($ttl, $clock);

        return new self($idToken, $tenantId, $ttl, $clock);
    }

    /**
     * @return string
     */
    public function idToken() {
        return $this->idToken;
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->tenantId;
    }

    /**
     * @return DateInterval
     */
    public function ttl() {
        return $this->ttl;
    }

    /**
     * @return int
     */
    public function ttlInSeconds() {
        $now = $this->clock->now();

        return $now->add($this->ttl)->getTimestamp() - $now->getTimestamp();
    }

    /**
     * @param int|DateInterval $ttl
     *
     * @throws CVendor_Firebase_Exception_InvalidArgumentException
     *
     * @return DateInterval
     */
    private static function assertValidDuration($ttl, ClockInterface $clock) {
        if (\is_int($ttl)) {
            if ($ttl < 0) {
                throw new CVendor_Firebase_Exception_InvalidArgumentException('A session cookie cannot be valid for a negative amount of time');
            }

            $ttl = new DateInterval('PT' . $ttl . 'S');
        }

        $now = $clock->now();

        $expiresAt = $now->add($ttl);

        $min = $now->add(new DateInterval(self::FIVE_MINUTES));
        $max = $now->add(new DateInterval(self::TWO_WEEKS));

        if ($expiresAt >= $min && $expiresAt <= $max) {
            return $ttl;
        }

        throw new CVendor_Firebase_Exception_InvalidArgumentException('The TTL of a session must be between 5 minutes and 14 days');
    }
}
