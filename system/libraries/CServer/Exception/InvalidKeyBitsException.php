<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Ukuran bit tidak sah untuk jenis kunci yang dipilih.
 *
 * Ukuran yang sah bergantung pada jenisnya — RSA menerima 2048 ke atas,
 * sedangkan ECDSA hanya menerima tiga nilai tertentu. Karena itu jenis dan
 * daftar ukuran yang sah dibawa bersama galatnya.
 */
class CServer_Exception_InvalidKeyBitsException extends CServer_Exception {
    /**
     * @var null|int
     */
    protected $bits;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $validBitsList;

    /**
     * @param null|int $bits
     * @param string   $type
     */
    public function __construct($bits, $type, array $validBitsList = []) {
        $this->bits = $bits === null ? null : (int) $bits;
        $this->type = (string) $type;
        $this->validBitsList = $validBitsList;

        parent::__construct('Ukuran bit :bits tidak sah untuk kunci :type. Yang sah: :list.', [
            ':bits' => (string) $bits,
            ':type' => (string) $type,
            ':list' => count($validBitsList) > 0 ? implode(', ', $validBitsList) : '-',
        ]);
    }

    /**
     * @return null|int
     */
    public function getBits() {
        return $this->bits;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getValidBitsList() {
        return $this->validBitsList;
    }
}
