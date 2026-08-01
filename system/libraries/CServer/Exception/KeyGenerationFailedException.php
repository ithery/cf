<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * ssh-keygen dipanggil tetapi tidak menghasilkan pasangan kunci yang utuh.
 *
 * Keluaran mentahnya dibawa terpisah dari pesan: pesan dipendekkan agar layak
 * ditampilkan ke pengguna, sedangkan keluaran penuh tetap tersedia untuk dicatat
 * ke log saat menelusuri sebabnya.
 */
class CServer_Exception_KeyGenerationFailedException extends CServer_Exception {
    /**
     * @var string
     */
    protected $output;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @param string $reason
     * @param string $output
     */
    public function __construct($reason, $output = '') {
        $this->reason = (string) $reason;
        $this->output = (string) $output;

        parent::__construct('Pembuatan kunci SSH gagal: :reason', [':reason' => (string) $reason]);
    }

    /**
     * @return string keluaran mentah perintah, mungkin kosong
     */
    public function getOutput() {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }
}
