<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Kegagalan saat memanggil REST API Mailjet.
 *
 * Kode HTTP-nya disimpan terpisah dari pesan: 401 berarti kredensialnya salah,
 * 404 berarti sumber dayanya belum ada — dua hal yang penanganannya berbeda dan
 * tidak layak dibedakan dengan mencocokkan teks pesan.
 */
class CVendor_Mailjet_Exception extends CException {
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @param string     $message
     * @param int        $code
     * @param int        $statusCode
     * @param null|mixed $previous
     */
    public function __construct($message, $code = 0, $statusCode = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->statusCode = (int) $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @return bool
     */
    public function isNotFound() {
        return $this->statusCode == 404;
    }
}
