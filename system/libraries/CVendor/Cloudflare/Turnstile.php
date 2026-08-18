<?php

/**
 * Cloudflare Turnstile, pemeriksa pengunjung manusia.
 *
 * Hanya sisi server. Widget-nya dipasang klien lewat skrip Cloudflare, jadi
 * kelas ini tidak membangun HTML apa pun.
 */
class CVendor_Cloudflare_Turnstile {
    /**
     * Nama field yang dikirim widget.
     *
     * @var string
     */
    const RESPONSE_FIELD = 'cf-turnstile-response';

    /**
     * @var string
     */
    const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * @var string
     */
    const SCRIPT_URL = 'https://challenges.cloudflare.com/turnstile/v0/api.js';

    /**
     * @var int
     */
    const TIMEOUT_SECOND = 10;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $siteKey;

    /**
     * @param string $secretKey
     * @param string $siteKey
     */
    public function __construct($secretKey, $siteKey) {
        $this->secretKey = (string) $secretKey;
        $this->siteKey = (string) $siteKey;
    }

    /**
     * @return string
     */
    public function getSiteKey() {
        return $this->siteKey;
    }

    /**
     * @return string
     */
    public function getScriptUrl() {
        return static::SCRIPT_URL;
    }

    /**
     * Kunci sudah terpasang atau belum.
     *
     * @return bool
     */
    public function isConfigured() {
        return strlen($this->secretKey) > 0 && strlen($this->siteKey) > 0;
    }

    /**
     * Memeriksa token widget ke Cloudflare.
     *
     * @param null|string $token
     * @param null|string $remoteIp
     *
     * @return CVendor_Cloudflare_Turnstile_Response
     */
    public function verify($token, $remoteIp = null) {
        if (!is_string($token) || strlen($token) === 0) {
            return CVendor_Cloudflare_Turnstile_Response::failed(['missing-input-response']);
        }

        $form = [
            'secret' => $this->secretKey,
            'response' => $token,
        ];

        if (strlen((string) $remoteIp) > 0) {
            $form['remoteip'] = $remoteIp;
        }

        try {
            $response = CHTTP::client()
                ->timeout(static::TIMEOUT_SECOND)
                ->asForm()
                ->post(static::VERIFY_URL, $form);
        } catch (Exception $ex) {
            return CVendor_Cloudflare_Turnstile_Response::failed(['internal-error'], $ex->getMessage());
        }

        if (!$response->successful()) {
            return CVendor_Cloudflare_Turnstile_Response::failed(
                ['internal-error'],
                'HTTP ' . $response->status()
            );
        }

        return CVendor_Cloudflare_Turnstile_Response::fromArray((array) $response->json());
    }
}
