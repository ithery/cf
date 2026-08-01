<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Pengelolaan certbot pada sebuah server.
 *
 * Berbeda dari CServer_LetsEncrypt yang mengurus penempatan berkas sertifikat
 * dan tantangan webroot, kelas ini mengurus certbot sebagai perangkat lunak:
 * memeriksa keterpasangan, memasang, membaca sertifikat yang dikelolanya, dan
 * memperpanjang.
 */
class CServer_Certbot {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @param CServer_Server $server
     */
    public function __construct(CServer_Server $server) {
        $this->server = $server;
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @param array|string $command
     *
     * @return string
     */
    protected function run($command) {
        return $this->server->runCommand($command);
    }

    /**
     * Versi certbot yang terpasang, atau null bila tidak ada.
     *
     * Memakai `command -v` lebih dulu agar tidak bergantung pada pesan galat
     * shell yang berbeda-beda antar distribusi.
     *
     * @return null|string
     */
    public function getVersion() {
        $output = trim($this->run('command -v certbot >/dev/null 2>&1 && certbot --version 2>&1 || echo CERTBOT_NOT_FOUND'));
        if (strlen($output) == 0 || strpos($output, 'CERTBOT_NOT_FOUND') !== false) {
            return null;
        }

        //keluarannya berbentuk "certbot 2.9.0"
        if (preg_match('/certbot\s+([0-9][0-9a-zA-Z.\-]*)/i', $output, $m)) {
            return $m[1];
        }

        return $output;
    }

    /**
     * @return bool
     */
    public function isInstalled() {
        return $this->getVersion() !== null;
    }

    /**
     * Pengelola paket yang tersedia di server ini, diurutkan dari yang paling
     * dianjurkan. Snap adalah cara resmi yang direkomendasikan certbot.
     *
     * @return array kunci metode => label
     */
    public function getInstallMethodList() {
        $probe = trim($this->run(
            'echo "snap:$(command -v snap >/dev/null 2>&1 && echo yes || echo no)";'
            . ' echo "apt:$(command -v apt-get >/dev/null 2>&1 && echo yes || echo no)";'
            . ' echo "dnf:$(command -v dnf >/dev/null 2>&1 && echo yes || echo no)";'
            . ' echo "yum:$(command -v yum >/dev/null 2>&1 && echo yes || echo no)"'
        ));

        $available = [];
        foreach (explode("\n", $probe) as $line) {
            $line = trim($line);
            if (strpos($line, ':') === false) {
                continue;
            }
            list($name, $status) = explode(':', $line, 2);
            $available[trim($name)] = trim($status) == 'yes';
        }

        $label = [
            'snap' => 'Snap (recommended by certbot)',
            'apt' => 'APT (Debian/Ubuntu)',
            'dnf' => 'DNF (Fedora/RHEL 8+)',
            'yum' => 'YUM (CentOS 7)',
        ];

        $method = [];
        foreach ($label as $key => $text) {
            if (carr::get($available, $key)) {
                $method[$key] = $text;
            }
        }

        return $method;
    }

    /**
     * Perintah pemasangan untuk sebuah metode.
     *
     * Dipisah dari install() supaya pemanggil dapat menampilkannya lebih dulu.
     * Memasang paket di server produksi sebaiknya tidak terjadi tanpa pengguna
     * tahu persis apa yang akan dijalankan.
     *
     * @param string $method
     *
     * @return array
     */
    public function getInstallCommand($method) {
        $command = [
            'snap' => [
                'snap install core',
                'snap refresh core',
                'snap install --classic certbot',
                'ln -sf /snap/bin/certbot /usr/bin/certbot',
            ],
            'apt' => [
                'apt-get update -y',
                'DEBIAN_FRONTEND=noninteractive apt-get install -y certbot',
            ],
            'dnf' => [
                'dnf install -y certbot',
            ],
            'yum' => [
                'yum install -y certbot',
            ],
        ];

        return carr::get($command, $method, []);
    }

    /**
     * @param string $method
     *
     * @return array errCode, errMessage, output, version
     */
    public function install($method) {
        $command = $this->getInstallCommand($method);
        if (count($command) == 0) {
            return ['errCode' => 1, 'errMessage' => 'Unknown install method: ' . $method, 'output' => '', 'version' => null];
        }

        try {
            $output = $this->run($command);
        } catch (Exception $ex) {
            return ['errCode' => 1, 'errMessage' => $ex->getMessage(), 'output' => '', 'version' => null];
        }

        $version = $this->getVersion();
        if ($version === null) {
            return [
                'errCode' => 1,
                'errMessage' => 'Install command finished but certbot is still not detected.',
                'output' => $output,
                'version' => null,
            ];
        }

        return ['errCode' => 0, 'errMessage' => '', 'output' => $output, 'version' => $version];
    }

    /**
     * Sertifikat yang dikelola certbot di server ini.
     *
     * Keluaran `certbot certificates` berupa teks, jadi diurai per blok. Bila
     * formatnya berubah, kolomnya kosong dan bukan salah tafsir.
     *
     * @return array tiap entri: name, domains, expiry, days, path
     */
    public function getCertificateList() {
        $output = $this->run('certbot certificates 2>&1');
        $list = [];
        $current = null;

        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (preg_match('/^Certificate Name:\s*(.+)$/i', $line, $m)) {
                if ($current != null) {
                    $list[] = $current;
                }
                $current = ['name' => trim($m[1]), 'domains' => '', 'expiry' => '', 'days' => null, 'path' => ''];

                continue;
            }
            if ($current == null) {
                continue;
            }
            if (preg_match('/^Domains:\s*(.+)$/i', $line, $m)) {
                $current['domains'] = trim($m[1]);
            } elseif (preg_match('/^Expiry Date:\s*(.+)$/i', $line, $m)) {
                $value = trim($m[1]);
                $current['expiry'] = $value;
                if (preg_match('/VALID:\s*(\d+)\s*day/i', $value, $d)) {
                    $current['days'] = (int) $d[1];
                } elseif (stripos($value, 'INVALID') !== false || stripos($value, 'EXPIRED') !== false) {
                    $current['days'] = 0;
                }
            } elseif (preg_match('/^Certificate Path:\s*(.+)$/i', $line, $m)) {
                $current['path'] = trim($m[1]);
            }
        }
        if ($current != null) {
            $list[] = $current;
        }

        return $list;
    }

    /**
     * Uji perpanjangan tanpa mengubah sertifikat apa pun.
     *
     * @return string
     */
    public function renewDryRun() {
        return $this->run('certbot renew --dry-run 2>&1');
    }

    /**
     * @return string
     */
    public function renew() {
        return $this->run('certbot renew --quiet 2>&1; echo "exit status $?"');
    }

    /**
     * Apakah perpanjangan otomatis terpasang. Certbot memasang timer systemd
     * atau entri cron tergantung cara pemasangannya, jadi keduanya diperiksa.
     *
     * @return array timer, cron, enabled
     */
    public function getAutoRenewStatus() {
        $output = $this->run(
            'echo "timer:$(systemctl list-timers --all 2>/dev/null | grep -c certbot)";'
            . ' echo "cron:$(ls /etc/cron.d/ 2>/dev/null | grep -c certbot)"'
        );
        $result = ['timer' => 0, 'cron' => 0];
        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (strpos($line, ':') === false) {
                continue;
            }
            list($key, $value) = explode(':', $line, 2);
            $key = trim($key);
            if (array_key_exists($key, $result)) {
                $result[$key] = (int) trim($value);
            }
        }
        $result['enabled'] = ($result['timer'] > 0 || $result['cron'] > 0);

        return $result;
    }
}
