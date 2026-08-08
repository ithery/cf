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
     * Tanda tangannya mengikuti CException, yang menyisipkan $variables sebagai
     * parameter kedua untuk penggantian penanda pada pesannya.
     *
     * @param string $message
     * @param int    $code
     * @param int    $statusCode
     */
    public function __construct($message = '', ?array $variables = null, $code = 0, ?Exception $previous = null, $statusCode = 0) {
        parent::__construct($message, $variables, $code, $previous);
        $this->statusCode = (int) $statusCode;
    }

    /**
     * Pembuat yang lebih ringkas untuk pemanggil yang hanya punya kode HTTP.
     *
     * @param string $message
     * @param int    $statusCode
     *
     * @return static
     */
    public static function fromStatus($message, $statusCode) {
        return new static($message, null, 0, null, $statusCode);
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
