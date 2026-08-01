<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Domain yang diminta tidak dapat diterbitkan sertifikatnya.
 *
 * Dipisahkan dari kegagalan certbot itu sendiri karena penyebabnya berbeda
 * sifat: ini ketahuan sebelum apa pun dijalankan, sehingga tidak memakan jatah
 * percobaan Let's Encrypt dan pemanggil dapat menampilkannya sebagai galat
 * masukan biasa, bukan sebagai kegagalan penerbitan.
 */
class CServer_Exception_InvalidDomainException extends CServer_Exception {
    /**
     * @var string
     */
    protected $reason;

    /**
     * @var array
     */
    protected $domainList;

    /**
     * @param string $reason
     */
    public function __construct($reason, array $domainList = []) {
        $this->reason = (string) $reason;
        $this->domainList = $domainList;

        parent::__construct('Domain tidak dapat diterbitkan: :reason', [':reason' => (string) $reason]);
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * @return array
     */
    public function getDomainList() {
        return $this->domainList;
    }
}
