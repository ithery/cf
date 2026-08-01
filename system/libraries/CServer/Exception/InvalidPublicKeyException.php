<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Baris kunci publik yang diberikan tidak dapat dipakai.
 *
 * Nilai yang ditolak ikut dibawa dalam bentuk terpotong. Kunci publik memang
 * bukan rahasia, tetapi memuat isinya utuh ke dalam pesan galat berarti ia akan
 * tersalin ke log dan ke layar tanpa memperjelas apa pun.
 */
class CServer_Exception_InvalidPublicKeyException extends CServer_Exception {
    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @param string $reason
     * @param string $publicKey
     */
    public function __construct($reason, $publicKey = '') {
        $this->reason = (string) $reason;
        $this->publicKey = (string) $publicKey;

        parent::__construct('Kunci publik tidak sah: :reason', [':reason' => (string) $reason]);
    }

    /**
     * @return string
     */
    public function getPublicKey() {
        return $this->publicKey;
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * Potongan kunci yang aman ditampilkan di antarmuka.
     *
     * @return string
     */
    public function getPublicKeyPreview() {
        return cstr::limit(trim($this->publicKey), 40);
    }
}
