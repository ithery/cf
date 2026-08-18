<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Distribusi yang tidak dikenali.
 *
 * Sengaja tidak menebak: getPackageManager() mengembalikan null dan
 * getInstallCommand() array kosong, sehingga pemanggil dapat memilih untuk
 * memeriksa sendiri lewat probe alih-alih menjalankan perintah yang belum tentu
 * ada di server itu.
 */
class CServer_OS_Unknown extends CServer_OSAbstract {
    /**
     * @return string
     */
    public function getFamily() {
        return 'unknown';
    }

    /**
     * @return null|string
     */
    public function getPackageManager() {
        return null;
    }

    /**
     * @param string $package
     *
     * @return array
     */
    public function getInstallCommand($package) {
        return [];
    }
}
