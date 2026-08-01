<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Sebuah perintah yang dibutuhkan tidak ada di mesin tempat ia dicari.
 *
 * Membawa keterangan lokasi pencarian, karena "ssh-keygen tidak ditemukan"
 * bermakna sangat berbeda antara mesin aplikasi dan server tujuan — yang
 * pertama masalah pemasangan kita, yang kedua masalah server pengguna.
 */
class CServer_Exception_CommandNotFoundException extends CServer_Exception {
    const LOCATION_LOCAL = 'local';

    const LOCATION_REMOTE = 'remote';

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $location;

    /**
     * @param string $command
     * @param string $location
     */
    public function __construct($command, $location = self::LOCATION_REMOTE) {
        $this->command = (string) $command;
        $this->location = (string) $location;

        $message = $location == self::LOCATION_LOCAL
            ? 'Perintah :command tidak tersedia pada server aplikasi ini.'
            : 'Perintah :command tidak tersedia pada server tujuan.';

        parent::__construct($message, [':command' => (string) $command]);
    }

    /**
     * @return string
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @return bool
     */
    public function isLocal() {
        return $this->location == self::LOCATION_LOCAL;
    }
}
