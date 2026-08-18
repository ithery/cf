<?php

/**
 * Jawaban siteverify Turnstile.
 */
class CVendor_Cloudflare_Turnstile_Response {
    /**
     * @var bool
     */
    protected $success = false;

    /**
     * @var array
     */
    protected $errorCodes = [];

    /**
     * @var null|string
     */
    protected $hostname;

    /**
     * @var null|string
     */
    protected $challengeTs;

    /**
     * @var null|string
     */
    protected $action;

    /**
     * @var null|string
     */
    protected $cdata;

    /**
     * Galat di sisi kita, di luar jawaban Cloudflare.
     *
     * @var null|string
     */
    protected $internalMessage;

    /**
     * @return CVendor_Cloudflare_Turnstile_Response
     */
    public static function fromArray(array $payload) {
        $response = new static();
        $response->success = (bool) carr::get($payload, 'success', false);
        $response->errorCodes = (array) carr::get($payload, 'error-codes', []);
        $response->hostname = carr::get($payload, 'hostname');
        $response->challengeTs = carr::get($payload, 'challenge_ts');
        $response->action = carr::get($payload, 'action');
        $response->cdata = carr::get($payload, 'cdata');

        return $response;
    }

    /**
     * @param null|string $internalMessage
     *
     * @return CVendor_Cloudflare_Turnstile_Response
     */
    public static function failed(array $errorCodes, $internalMessage = null) {
        $response = new static();
        $response->success = false;
        $response->errorCodes = $errorCodes;
        $response->internalMessage = $internalMessage;

        return $response;
    }

    /**
     * @return bool
     */
    public function isSuccess() {
        return $this->success;
    }

    /**
     * @return array
     */
    public function getErrorCodes() {
        return $this->errorCodes;
    }

    /**
     * @return null|string
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * @return null|string
     */
    public function getChallengeTs() {
        return $this->challengeTs;
    }

    /**
     * @return null|string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @return null|string
     */
    public function getCdata() {
        return $this->cdata;
    }

    /**
     * @return null|string
     */
    public function getInternalMessage() {
        return $this->internalMessage;
    }

    /**
     * Kegagalan karena hulu, bukan karena pengunjungnya.
     *
     * Dibedakan supaya pemanggil bisa memilih tetap melayani saat Cloudflare
     * tak terjangkau, alih-alih menolak semua orang.
     *
     * @return bool
     */
    public function isInternalError() {
        return in_array('internal-error', $this->errorCodes, true);
    }

    /**
     * @return string
     */
    public function getErrorMessage() {
        if ($this->internalMessage !== null) {
            return $this->internalMessage;
        }

        return count($this->errorCodes) > 0 ? implode(', ', $this->errorCodes) : '';
    }
}
