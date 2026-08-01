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

        //Urutan mengikuti distribusi server: pengelola paket bawaannya
        //didahulukan setelah snap, supaya CentOS tidak ditawari apt hanya
        //karena kebetulan ada. Probe di atas tetap menentukan — daftar ini
        //hanya berisi yang benar-benar terpasang.
        $preferred = $this->server->distro()->getPackageManager();
        $order = ['snap'];
        if ($preferred !== null) {
            $order[] = $preferred;
        }
        foreach (array_keys($label) as $key) {
            if (!in_array($key, $order)) {
                $order[] = $key;
            }
        }

        $method = [];
        foreach ($order as $key) {
            if (carr::get($available, $key) && isset($label[$key])) {
                $method[$key] = $label[$key];
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
     * Perintah penerbitan sertifikat lewat verifikasi HTTP-01.
     *
     * Dipisah dari issue() dengan alasan yang sama seperti getInstallCommand():
     * perintah yang menyentuh server produksi sebaiknya bisa dibaca lebih dulu.
     *
     * Memakai --webroot, bukan --standalone, karena standalone perlu mengikat
     * port 80 sehingga web server harus dihentikan sebentar — pada server yang
     * sedang melayani itu berarti mati layanan hanya untuk menerbitkan
     * sertifikat.
     *
     * @param string      $webroot
     * @param null|string $email   null berarti --register-unsafely-without-email
     * @param bool        $dryRun
     *
     * @throws CServer_Exception_InvalidDomainException
     *
     * @return string
     */
    public function getIssueCommand(array $domainList, $webroot, $email = null, $dryRun = false) {
        $domainList = self::normalizeDomainList($domainList);
        if (count($domainList) == 0) {
            throw new CServer_Exception_InvalidDomainException('tidak ada domain yang diberikan');
        }
        if (strlen(trim((string) $webroot)) == 0) {
            throw new CServer_Exception_InvalidDomainException(
                'webroot tidak diketahui, verifikasi HTTP tidak dapat menaruh berkas tantangan'
            );
        }

        $command = 'certbot certonly --webroot -w ' . escapeshellarg(trim($webroot));
        foreach ($domainList as $domain) {
            $command .= ' -d ' . escapeshellarg($domain);
        }
        $command .= ' --non-interactive --agree-tos --keep-until-expiring';
        if (strlen((string) $email) > 0) {
            $command .= ' -m ' . escapeshellarg($email);
        } else {
            $command .= ' --register-unsafely-without-email';
        }
        if ($dryRun) {
            $command .= ' --dry-run';
        }

        return $command . ' 2>&1';
    }

    /**
     * Menerbitkan sertifikat untuk sekumpulan domain.
     *
     * Dianjurkan menjalankan dryRun lebih dulu: Let's Encrypt membatasi lima
     * kegagalan validasi per jam per domain, dan batas itu terpakai walau
     * kegagalannya sepele seperti salah webroot.
     *
     * @param string      $webroot
     * @param null|string $email
     * @param bool        $dryRun
     *
     * @return array errCode, errMessage, output, command, certificate
     */
    public function issue(array $domainList, $webroot, $email = null, $dryRun = false) {
        try {
            $command = $this->getIssueCommand($domainList, $webroot, $email, $dryRun);
        } catch (CServer_Exception $ex) {
            return ['errCode' => 1, 'errMessage' => $ex->getMessage(), 'output' => '', 'command' => '', 'certificate' => null];
        }

        if (!$this->isInstalled()) {
            return [
                'errCode' => 1,
                'errMessage' => 'certbot belum terpasang di server ini.',
                'output' => '', 'command' => $command, 'certificate' => null,
            ];
        }

        try {
            $output = (string) $this->run($command);
        } catch (Exception $ex) {
            return ['errCode' => 1, 'errMessage' => $ex->getMessage(), 'output' => '', 'command' => $command, 'certificate' => null];
        }

        $berhasil = $dryRun
            ? (stripos($output, 'dry run') !== false && stripos($output, 'successful') !== false)
            : (stripos($output, 'Successfully received certificate') !== false
                || stripos($output, 'Certificate not yet due for renewal') !== false
                || stripos($output, 'Congratulations') !== false);

        if (!$berhasil) {
            return [
                'errCode' => 1,
                'errMessage' => self::extractError($output),
                'output' => $output, 'command' => $command, 'certificate' => null,
            ];
        }

        //sertifikat hasilnya dicari berdasar domain pertama, sama seperti cara
        //certbot memberi nama lineage-nya
        $certificate = null;
        if (!$dryRun) {
            $nama = carr::get(self::normalizeDomainList($domainList), 0);
            foreach ($this->getCertificateList() as $item) {
                if (carr::get($item, 'name') == $nama) {
                    $certificate = $item;

                    break;
                }
            }
        }

        return ['errCode' => 0, 'errMessage' => '', 'output' => $output, 'command' => $command, 'certificate' => $certificate];
    }

    /**
     * Membersihkan dan memvalidasi daftar domain.
     *
     * Nilai seperti `*.contoh.com` dan `_` memang muncul di konfigurasi vhost
     * tetapi tidak dapat diterbitkan lewat HTTP-01 — wildcard hanya bisa lewat
     * DNS-01 — jadi disaring di sini alih-alih dibiarkan gagal di Let's Encrypt
     * dan memakan jatah percobaan.
     *
     * @return array
     */
    public static function normalizeDomainList(array $domainList) {
        $hasil = [];
        foreach ($domainList as $domain) {
            $domain = strtolower(trim((string) $domain));
            $domain = preg_replace('/^https?:\/\//', '', $domain);
            $domain = rtrim($domain, '/.');
            if (strlen($domain) == 0 || $domain == '_' || $domain == 'localhost') {
                continue;
            }
            if (strpos($domain, '*') !== false) {
                continue;
            }
            if (!preg_match('/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9\-]*[a-z0-9])?)+$/', $domain)) {
                continue;
            }
            if (!in_array($domain, $hasil)) {
                $hasil[] = $domain;
            }
        }

        return $hasil;
    }

    /**
     * Mengambil baris yang menjelaskan sebab kegagalan dari keluaran certbot.
     *
     * Keluarannya panjang dan sebagian besar hanya derau; yang berguna bagi
     * pengguna biasanya satu-dua baris.
     *
     * @param string $output
     *
     * @return string
     */
    protected static function extractError($output) {
        $penting = [];
        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            if (preg_match('/^(Domain:|Type:|Detail:|Error|.*(too many|rate limit|unauthorized|connection refused|timeout|NXDOMAIN|not a valid domain))/i', $line)) {
                $penting[] = $line;
            }
        }

        if (count($penting) == 0) {
            return cstr::limit(trim((string) $output), 300) ?: 'certbot gagal tanpa keterangan.';
        }

        return implode(' ', array_slice($penting, 0, 6));
    }

    /**
     * Uji perpanjangan tanpa mengubah sertifikat apa pun.
     *
     * @param null|string $certName null berarti seluruh sertifikat
     *
     * @return string
     */
    public function renewDryRun($certName = null) {
        return $this->run($this->getRenewCommand($certName, false, true));
    }

    /**
     * Perintah perpanjangan.
     *
     * --quiet sengaja tidak dipakai: keluarannya adalah satu-satunya cara
     * pemanggil tahu apa yang sebenarnya terjadi, dan halaman hasil yang kosong
     * membuat perpanjangan yang berhasil maupun yang tidak melakukan apa-apa
     * tampak sama saja.
     *
     * @param null|string $certName null berarti seluruh sertifikat
     * @param bool        $force
     * @param bool        $dryRun
     *
     * @return string
     */
    public function getRenewCommand($certName = null, $force = false, $dryRun = false) {
        $command = 'certbot renew';
        if (strlen((string) $certName) > 0) {
            $command .= ' --cert-name ' . escapeshellarg((string) $certName);
        }
        if ($force) {
            $command .= ' --force-renewal';
        }
        if ($dryRun) {
            $command .= ' --dry-run';
        }

        //Tanpa ini certbot tidur acak sampai sekitar sepuluh menit sebelum
        //bekerja — perilaku yang benar untuk cron, supaya tidak semua server di
        //dunia menembak Let's Encrypt pada menit yang sama, tetapi salah untuk
        //tombol yang ditekan manusia: yang terlihat hanyalah halaman yang
        //menggantung lalu gagal karena batas waktu koneksi, padahal certbot
        //belum melakukan apa-apa. Terlihat di log sebagai
        //"Non-interactive renewal: random delay of 469 seconds".
        $command .= ' --no-random-sleep-on-renew';

        return $command . ' --non-interactive 2>&1; echo "exit status $?"';
    }

    /**
     * Memperpanjang sertifikat.
     *
     * Tanpa $force, certbot hanya menyentuh sertifikat yang tinggal kurang dari
     * 30 hari — jadi memanggilnya untuk sertifikat yang masih lama memang tidak
     * melakukan apa pun, dan itu perilaku yang benar. $force menerbitkan ulang
     * apa pun keadaannya, tetapi memakan jatah Let's Encrypt: lima sertifikat
     * duplikat per minggu untuk kumpulan domain yang sama.
     *
     * @param null|string $certName
     * @param bool        $force
     *
     * @return string
     */
    public function renew($certName = null, $force = false) {
        return $this->run($this->getRenewCommand($certName, $force, false));
    }

    /**
     * @param string $certName
     *
     * @return string
     */
    public function getDeleteCommand($certName) {
        return 'certbot delete --cert-name ' . escapeshellarg((string) $certName)
            . ' --non-interactive 2>&1; echo "exit status $?"';
    }

    /**
     * Menghapus sebuah sertifikat beserta konfigurasi perpanjangannya.
     *
     * Keberadaannya diperiksa lebih dulu — satu perjalanan SSH tambahan, tetapi
     * penghapusan tidak dapat dibatalkan dan certbot sendiri hanya mengeluh
     * samar bila namanya salah ketik.
     *
     * @param string $certName
     *
     * @return array errCode, errMessage, output, command
     */
    public function delete($certName) {
        $certName = trim((string) $certName);
        if (strlen($certName) == 0) {
            return ['errCode' => 1, 'errMessage' => 'Nama sertifikat kosong.', 'output' => '', 'command' => ''];
        }

        $ada = false;
        foreach ($this->getCertificateList() as $item) {
            if (carr::get($item, 'name') === $certName) {
                $ada = true;

                break;
            }
        }
        if (!$ada) {
            return [
                'errCode' => 1,
                'errMessage' => 'Sertifikat ' . $certName . ' tidak ada pada certbot di server ini.',
                'output' => '', 'command' => '',
            ];
        }

        $command = $this->getDeleteCommand($certName);

        try {
            $output = (string) $this->run($command);
        } catch (Exception $ex) {
            return ['errCode' => 1, 'errMessage' => $ex->getMessage(), 'output' => '', 'command' => $command];
        }

        //certbot tidak selalu memberi kode keluar tidak nol saat gagal, jadi
        //hasilnya dipastikan dengan membaca ulang daftarnya
        foreach ($this->getCertificateList() as $item) {
            if (carr::get($item, 'name') === $certName) {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Sertifikat ' . $certName . ' masih ada setelah perintah hapus dijalankan.',
                    'output' => $output, 'command' => $command,
                ];
            }
        }

        return ['errCode' => 0, 'errMessage' => '', 'output' => $output, 'command' => $command];
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
