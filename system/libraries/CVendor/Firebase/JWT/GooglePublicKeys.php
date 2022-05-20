<?php

use GuzzleHttp\Client;
use Beste\Clock\SystemClock;
use Psr\Clock\ClockInterface;

final class CVendor_Firebase_JWT_GooglePublicKeys implements CVendor_Firebase_JWT_Contract_KeysInterface {
    /**
     * @var ClockInterface
     */
    private $clock;

    /**
     * @var CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_HandlerInterface
     */
    private $handler;

    /**
     * @var null|CVendor_Firebase_JWT_Contract_KeysInterface
     */
    private $keys = null;

    /**
     * @param null|CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_HandlerInterface $handler
     * @param null|ClockInterface                                                     $clock
     */
    public function __construct(CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_HandlerInterface $handler = null, ClockInterface $clock = null) {
        $this->clock = $clock ?: SystemClock::create();
        $this->handler = $handler ?: new CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_WithGuzzle(new Client(['http_errors' => false]), $this->clock);
    }

    /**
     * @return array
     */
    public function all() {
        $keysAreThereButExpired = $this->keys instanceof CVendor_Firebase_JWT_Contract_ExpirableInterface && $this->keys->isExpiredAt($this->clock->now());

        if (!$this->keys || $keysAreThereButExpired) {
            $this->keys = $this->handler->handle(CVendor_Firebase_JWT_Action_FetchGooglePublicKeys::fromGoogle());
            // There is a small chance that we get keys that are already expired, but at this point we're happy
            // that we got keys at all. The next time this method gets called, we will re-fetch.
        }

        return $this->keys->all();
    }
}
