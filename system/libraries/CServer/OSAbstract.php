<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Pengetahuan khas satu distribusi Linux pada server remote.
 *
 * Sebelum ini kelasnya kosong dan tiap pemeriksa menebak sendiri lewat kondisi
 * shell — mencoba `command -v apt-get`, lalu `dnf`, lalu `yum`, dan mencari
 * berkas konfigurasi di beberapa lokasi sekaligus. Cara itu tetap berguna
 * sebagai jaring pengaman, tetapi menaruh jawabannya di satu tempat membuat
 * perbedaan antar distribusi terbaca jelas alih-alih tersebar sebagai rantai
 * `||` di dalam perintah.
 *
 * Dipilih lewat CServer_OSAbstract::detect(), yang membaca /etc/os-release di
 * server tujuan.
 */
abstract class CServer_OSAbstract {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var array
     */
    protected $release = [];

    /**
     * @param CServer_Server $server
     * @param array          $release isi /etc/os-release yang sudah diurai
     */
    public function __construct(CServer_Server $server, array $release = []) {
        $this->server = $server;
        $this->release = $release;
    }

    /**
     * Membaca /etc/os-release di server dan mengembalikan turunan yang cocok.
     *
     * ID_LIKE ikut diperiksa, sehingga turunan seperti Rocky, AlmaLinux, atau
     * Linux Mint tertangani tanpa perlu didaftarkan satu per satu.
     *
     * @param CServer_Server $server
     *
     * @return CServer_OSAbstract
     */
    public static function detect(CServer_Server $server) {
        $output = $server->runCommand(
            'cat /etc/os-release 2>/dev/null || (cat /etc/redhat-release 2>/dev/null && echo "ID=rhel")'
        );

        $release = [];
        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (strpos($line, '=') === false) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2);
            $release[strtoupper(trim($key))] = trim($value, " \t\"'");
        }

        $id = strtolower(carr::get($release, 'ID', ''));
        $like = strtolower(carr::get($release, 'ID_LIKE', ''));
        $haystack = $id . ' ' . $like;

        if (preg_match('/\b(debian|ubuntu)\b/', $haystack)) {
            return new CServer_OS_Debian($server, $release);
        }
        if (preg_match('/\b(rhel|centos|fedora|rocky|almalinux)\b/', $haystack)) {
            return new CServer_OS_RedHat($server, $release);
        }

        return new CServer_OS_Unknown($server, $release);
    }

    /**
     * @return string
     */
    public function getId() {
        return strtolower((string) carr::get($this->release, 'ID', 'unknown'));
    }

    /**
     * @return string
     */
    public function getVersion() {
        return (string) carr::get($this->release, 'VERSION_ID', '');
    }

    /**
     * @return string
     */
    public function getName() {
        return (string) carr::get($this->release, 'PRETTY_NAME', $this->getId());
    }

    /**
     * @return array
     */
    public function getRelease() {
        return $this->release;
    }

    /**
     * Keluarga distribusi: debian, rhel, atau unknown.
     *
     * @return string
     */
    abstract public function getFamily();

    /**
     * Pengelola paket bawaan distribusi ini.
     *
     * @return null|string
     */
    abstract public function getPackageManager();

    /**
     * Perintah memasang sebuah paket, tanpa interaksi.
     *
     * @param string $package
     *
     * @return array
     */
    abstract public function getInstallCommand($package);

    /**
     * Nama unit systemd untuk sebuah layanan, karena penamaannya berbeda antar
     * distribusi — Apache adalah `apache2` di Debian dan `httpd` di RHEL.
     *
     * @param string $service
     *
     * @return string
     */
    public function getServiceUnit($service) {
        return carr::get($this->serviceUnitMap(), $service, $service);
    }

    /**
     * @return array
     */
    protected function serviceUnitMap() {
        return [];
    }

    /**
     * Lokasi berkas konfigurasi yang lazim untuk sebuah perangkat lunak.
     *
     * @param string $software
     *
     * @return array daftar kandidat, diurut dari yang paling mungkin
     */
    public function getConfigPathList($software) {
        return carr::get($this->configPathMap(), $software, []);
    }

    /**
     * @return array
     */
    protected function configPathMap() {
        return [];
    }
}
