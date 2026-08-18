<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_SSHRepository {
    /**
     * @var array
     */
    protected $sshArray = [];

    /**
     * @var CServer_SSHRepository
     */
    private static $instance;

    private function __construct() {
        $this->sshArray = [];
    }

    /**
     * @return CServer_SSHRepository
     */
    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new CServer_SSHRepository();
        }

        return self::$instance;
    }

    /**
     * @param CRemote_SSH|CRemote_SSH_Config|array $sshConfig
     *
     * @return CRemote_SSH
     */
    public function getSSH($sshConfig) {
        $ssh = null;
        if ($sshConfig instanceof CRemote_SSH) {
            $ssh = $sshConfig;
            $sshConfig = $ssh->getConfig();
        }

        if (!$sshConfig instanceof CRemote_SSH_Config) {
            $sshConfig = new CRemote_SSH_Config($sshConfig);
        }

        $key = $this->resolveKey($sshConfig);
        if (!isset($this->sshArray[$key])) {
            if ($ssh === null) {
                $ssh = CRemote::ssh($sshConfig);
            }
            $this->sshArray[$key] = $ssh;
        }

        return $this->sshArray[$key];
    }

    /**
     * @return string
     */
    protected function resolveKey(CRemote_SSH_Config $config) {
        $host = $config->getConnectionHost();
        $port = $config->getPort() ?: 22;
        $username = $config->getUsername() ?: 'root';

        //Batas waktu ikut menentukan identitas koneksi. Tanpa ini, koneksi
        //pertama ke sebuah server menetapkan batas waktu untuk seluruh
        //permintaan: pemeriksaan cepat yang berjalan lebih dulu (biasanya 10
        //detik) akan dipakai ulang oleh perintah yang memang lama — memasang
        //paket, menerbitkan sertifikat — dan yang terjadi bukan galat melainkan
        //keluaran terpotong di tengah, sementara perintahnya tetap selesai di
        //server. Hasilnya tampak gagal padahal berhasil.
        $timeout = (int) $config->getTimeout();

        return $host . ':' . $port . ':' . $username . ':' . $timeout;
    }
}
