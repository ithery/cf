<?php

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Cookie;

class CApi_OAuth_ApiTokenCookieFactory {
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
    }

    /**
     * Create a new API token cookie.
     *
     * @param mixed  $userId
     * @param string $csrfToken
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function make($userId, $csrfToken) {
        $config = $this->config->get('session');

        $expiration = Carbon::now()->addMinutes($config['lifetime']);

        return new Cookie(
            CApi::oauth()->cookie(),
            $this->createToken($userId, $csrfToken, $expiration),
            $expiration,
            $config['path'],
            $config['domain'],
            $config['secure'],
            true,
            false,
            $config['same_site'] ?? null
        );
    }

    /**
     * Create a new JWT token for the given user ID and CSRF token.
     *
     * @param mixed          $userId
     * @param string         $csrfToken
     * @param \Carbon\Carbon $expiration
     *
     * @return string
     */
    protected function createToken($userId, $csrfToken, Carbon $expiration) {
        return JWT::encode([
            'sub' => $userId,
            'csrf' => $csrfToken,
            'expiry' => $expiration->getTimestamp(),
        ], CApi::oauth()->tokenEncryptionKey($this->encrypter), 'HS256');
    }
}
