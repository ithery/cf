<?php

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;

final class CVendor_Firebase_Auth_SignInResult {
    /**
     * @var null|string
     */
    private $idToken = null;

    /**
     * @var null|string
     */
    private $accessToken = null;

    /**
     * @var null|string
     */
    private $refreshToken = null;

    /**
     * @var null|int
     */
    private $ttl = null;

    /**
     * @var array<string, mixed>
     */
    private $data = [];

    /**
     * @var null|string
     */
    private $firebaseUserId = null;

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @var Configuration
     */
    private Configuration $config;

    private function __construct() {
        $this->config = Configuration::forUnsecuredSigner();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return self
     */
    public static function fromData(array $data) {
        $instance = new self();

        if ($expiresIn = $data['expiresIn'] ?? $data['expires_in'] ?? null) {
            $instance->ttl = (int) $expiresIn;
        }

        $instance->idToken = $data['idToken'] ?? $data['id_token'] ?? null;
        $instance->accessToken = $data['accessToken'] ?? $data['access_token'] ?? null;
        $instance->refreshToken = $data['refreshToken'] ?? $data['refresh_token'] ?? null;
        $instance->data = $data;

        return $instance;
    }

    /**
     * @return null|string
     */
    public function idToken() {
        return $this->idToken;
    }

    /**
     * @return null|string
     */
    public function firebaseUserId() {
        // @codeCoverageIgnoreStart
        if ($this->firebaseUserId) {
            return $this->firebaseUserId;
        }
        // @codeCoverageIgnoreEnd

        if ($this->idToken) {
            $idToken = $this->config->parser()->parse($this->idToken);
            \assert($idToken instanceof UnencryptedToken);

            foreach (['sub', 'localId', 'user_id'] as $claim) {
                if ($uid = $idToken->claims()->get($claim, false)) {
                    return $this->firebaseUserId = $uid;
                }
            }
        }

        if ($localId = $this->data['localId'] ?? null) {
            return $this->firebaseUserId = $localId;
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function firebaseTenantId() {
        if ($this->tenantId) {
            return $this->tenantId;
        }

        if ($this->idToken) {
            $idToken = $this->config->parser()->parse($this->idToken);
            \assert($idToken instanceof UnencryptedToken);

            $firebaseClaims = $idToken->claims()->get('firebase', new \stdClass());

            if (\is_object($firebaseClaims) && \property_exists($firebaseClaims, 'tenant')) {
                return $this->tenantId = $firebaseClaims->tenant;
            }

            if (\is_array($firebaseClaims) && \array_key_exists('tenant', $firebaseClaims)) {
                return $this->tenantId = $firebaseClaims['tenant'];
            }
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function accessToken() {
        return $this->accessToken;
    }

    /**
     * @return null|string
     */
    public function refreshToken() {
        return $this->refreshToken;
    }

    /**
     * @return null|int
     */
    public function ttl() {
        return $this->ttl;
    }

    /**
     * @return array<string, mixed>
     */
    public function data() {
        return $this->data;
    }

    /**
     * @return array<string, mixed>
     */
    public function asTokenResponse() {
        return [
            'token_type' => 'Bearer',
            'access_token' => $this->accessToken(),
            'id_token' => $this->idToken,
            'refresh_token' => $this->refreshToken(),
            'expires_in' => $this->ttl(),
        ];
    }
}
