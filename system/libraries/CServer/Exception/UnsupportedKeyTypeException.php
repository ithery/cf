<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Jenis kunci SSH yang diminta tidak dikenali.
 *
 * Daftar jenis yang didukung ikut dibawa, sehingga pemanggil dapat menampilkan
 * pilihan yang sah tanpa perlu memanggil balik ke CRemote_SSH_Key.
 */
class CServer_Exception_UnsupportedKeyTypeException extends CServer_Exception {
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $supportedTypeList;

    /**
     * @param string $type
     */
    public function __construct($type, array $supportedTypeList = []) {
        $this->type = (string) $type;
        $this->supportedTypeList = $supportedTypeList;

        parent::__construct('Jenis kunci SSH :type tidak didukung. Yang didukung: :list.', [
            ':type' => (string) $type,
            ':list' => count($supportedTypeList) > 0 ? implode(', ', array_keys($supportedTypeList)) : '-',
        ]);
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return array kunci jenis => label
     */
    public function getSupportedTypeList() {
        return $this->supportedTypeList;
    }
}
