<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Pemeriksa layanan surel pada sebuah server.
 *
 * Keberadaan paketnya saja tidak cukup untuk menyebut sebuah mesin "server
 * mail": hampir semua distribusi memasang MTA lokal (Postfix atau sendmail)
 * hanya agar cron dapat mengirim laporan ke root. Yang membedakan adalah
 * apakah ia benar-benar mendengarkan di antarmuka publik pada porta surel.
 * Karena itu kelas ini memeriksa keduanya dan melaporkannya terpisah.
 */
class CServer_Mail {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * Porta yang menandakan layanan surel sungguhan, beserta perannya.
     *
     * @var array
     */
    protected static $portRole = [
        25 => 'smtp',
        465 => 'smtps',
        587 => 'submission',
        110 => 'pop3',
        995 => 'pop3s',
        143 => 'imap',
        993 => 'imaps',
    ];

    /**
     * Penyedia relai yang dikenali dari nama hostnya.
     *
     * @var array
     */
    protected static $relayProvider = [
        'mailjet' => 'Mailjet',
        'sendgrid' => 'SendGrid',
        'amazonaws' => 'Amazon SES',
        'mailgun' => 'Mailgun',
        'postmark' => 'Postmark',
        'sparkpost' => 'SparkPost',
        'gmail' => 'Gmail',
        'office365' => 'Microsoft 365',
        'outlook' => 'Microsoft 365',
        'zoho' => 'Zoho',
        'brevo' => 'Brevo',
        'sendinblue' => 'Brevo',
    ];

    /**
     * @var null|string
     */
    private $sudoPrefix;

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
     * Penyedia relai yang didukung beserta bentuk kredensialnya.
     *
     * Ketiganya berbeda bukan hanya di nama host: SendGrid memakai kata
     * `apikey` harfiah sebagai nama pengguna dan API key sebagai kata sandi,
     * sedangkan Mailjet dan MailerSend memakai sepasang nilai. Perbedaan itu
     * jadi sumber kebingungan yang lazim, jadi dijelaskan di sini alih-alih
     * dibiarkan ditebak.
     *
     * @return array
     */
    public static function providerList() {
        return [
            'mailjet' => [
                'label' => 'Mailjet',
                'host' => 'in-v3.mailjet.com',
                'port' => 587,
                'portList' => [587, 588, 465, 25, 2525],
                'usernameLabel' => 'API Key',
                'passwordLabel' => 'Secret Key',
                'usernameFixed' => null,
                'docUrl' => 'https://app.mailjet.com/account/apikeys',
                'steps' => [
                    'Masuk ke akun Mailjet, buka menu Account Settings.',
                    'Pilih "API Key Management (REST API, SMTP relay)".',
                    'Salin API Key sebagai nama pengguna, dan Secret Key sebagai kata sandi.',
                    'Keduanya sama dengan kunci REST API — memegangnya berarti dapat mengirim'
                        . ' atas nama domain Anda, jadi jangan dibagikan.',
                ],
            ],
            'sendgrid' => [
                'label' => 'SendGrid',
                'host' => 'smtp.sendgrid.net',
                'port' => 587,
                'portList' => [587, 465, 25, 2525],
                'usernameLabel' => 'Nama Pengguna',
                'passwordLabel' => 'API Key',
                //SendGrid selalu memakai kata "apikey" harfiah sebagai nama
                //pengguna; yang berubah hanya kata sandinya
                'usernameFixed' => 'apikey',
                'docUrl' => 'https://app.sendgrid.com/settings/api_keys',
                'steps' => [
                    'Masuk ke SendGrid, buka Settings lalu API Keys.',
                    'Buat API Key baru dengan izin "Mail Send".',
                    'Nama penggunanya selalu kata <code>apikey</code> — bukan alamat surel Anda.',
                    'API Key yang baru dibuat hanya ditampilkan sekali; simpan sebelum menutup halamannya.',
                ],
            ],
            'mailersend' => [
                'label' => 'MailerSend',
                'host' => 'smtp.mailersend.net',
                'port' => 587,
                'portList' => [587, 2525],
                'usernameLabel' => 'SMTP Username',
                'passwordLabel' => 'SMTP Password',
                'usernameFixed' => null,
                'docUrl' => 'https://app.mailersend.com/domains',
                'steps' => [
                    'Masuk ke MailerSend, buka Domains lalu pilih domain yang sudah terverifikasi.',
                    'Buka tab SMTP, lalu buat SMTP user baru.',
                    'Salin Username dan Password yang muncul.',
                    'Domainnya harus terverifikasi lebih dulu; SMTP user tidak dapat dibuat sebelum itu.',
                ],
            ],
        ];
    }

    /**
     * @param string $provider
     *
     * @return null|array
     */
    public static function provider($provider) {
        return carr::get(self::providerList(), (string) $provider);
    }

    /**
     * Nama pengguna relai yang sedang dipakai.
     *
     * Hanya bagian pengguna yang dikembalikan — ia pengenal, bukan rahasia.
     * Kata sandinya dibaca terpisah lewat getRelaySecret(), supaya tidak ikut
     * terbawa setiap kali halaman dibuka.
     *
     * @return null|string
     */
    public function getRelayUsername() {
        $baris = $this->readRelayCredentialLine();
        if ($baris === null) {
            return null;
        }

        return carr::get($baris, 'username');
    }

    /**
     * Kata sandi relai. Dipanggil hanya saat pengguna memintanya secara
     * eksplisit.
     *
     * @return null|string
     */
    public function getRelaySecret() {
        $baris = $this->readRelayCredentialLine();
        if ($baris === null) {
            return null;
        }

        return carr::get($baris, 'password');
    }

    /**
     * Membaca satu baris kredensial dari berkas peta SASL.
     *
     * @return null|array username, password
     */
    protected function readRelayCredentialLine() {
        $berkas = trim((string) $this->run(
            $this->sudo() . 'postconf -h smtp_sasl_password_maps 2>/dev/null'
        ));
        //nilainya berbentuk "hash:/etc/postfix/sasl_passwd"
        if (strpos($berkas, ':') !== false) {
            $berkas = trim(substr($berkas, strpos($berkas, ':') + 1));
        }
        if (strlen($berkas) == 0) {
            return null;
        }

        $isi = (string) $this->run($this->sudo() . 'cat ' . escapeshellarg($berkas) . ' 2>/dev/null');
        foreach (explode("\n", $isi) as $line) {
            $line = trim($line);
            if (strlen($line) == 0 || substr($line, 0, 1) == '#') {
                continue;
            }
            //bentuknya "[host]:port user:pass"; pemisah pertama pada bagian
            //kredensial yang menentukan, karena kata sandi dapat memuat titik dua
            $bagian = preg_split('/\s+/', $line, 2);
            $kredensial = trim((string) carr::get($bagian, 1));
            if (strlen($kredensial) == 0) {
                continue;
            }
            $pos = strpos($kredensial, ':');
            if ($pos === false) {
                continue;
            }

            return [
                'username' => substr($kredensial, 0, $pos),
                'password' => substr($kredensial, $pos + 1),
            ];
        }

        return null;
    }

    /**
     * Awalan sudo bila koneksinya bukan root.
     *
     * @return string
     */
    protected function sudo() {
        if ($this->sudoPrefix !== null) {
            return $this->sudoPrefix;
        }
        $probe = trim($this->run(
            'if [ "$(id -u)" = "0" ]; then echo ROOT;'
            . ' elif sudo -n true >/dev/null 2>&1; then echo SUDO;'
            . ' else echo NONE; fi'
        ));
        $this->sudoPrefix = strpos($probe, 'SUDO') !== false ? 'sudo -n ' : '';

        return $this->sudoPrefix;
    }

    /**
     * Keadaan layanan surel di server ini.
     *
     * Seluruhnya diambil dalam satu perintah: tiap perjalanan SSH mahal, dan
     * pemeriksaan ini dijalankan untuk banyak server sekaligus.
     *
     * @return array mta, imap, listening, public, isMailServer
     */
    public function inspect() {
        //tiap kandidat dipisah koma, bukan ditempel: postfix memasang binari
        //`sendmail` sebagai kompatibilitas, sehingga keduanya cocok sekaligus
        //dan tanpa pemisah hasilnya terbaca "postfixsendmail"
        $output = (string) $this->run(
            'echo "MTA:$(for b in postfix exim sendmail; do'
            . ' command -v $b >/dev/null 2>&1 && printf "%s," "$b"; done)";'
            . ' echo "IMAP:$(for b in dovecot cyrus-master; do'
            . ' command -v $b >/dev/null 2>&1 && printf "%s," "$b"; done)";'
            //ss lebih umum tersedia daripada netstat pada distribusi baru,
            //tetapi netstat masih ada di yang lama — dua-duanya dicoba
            //relayhost dibaca sekalian: penyedia cloud lazim memblokir porta 25
            //keluar, sehingga server surel di sana harus merelai lewat pihak
            //ketiga — dan tanpa keterangan ini halaman surel tampak seolah
            //server mengirim sendiri
            . ' echo "RELAY:$(postconf -h relayhost 2>/dev/null'
            . ' || grep -m1 -E "^relayhost[[:space:]]*=" /etc/postfix/main.cf 2>/dev/null | cut -d= -f2)";'
            . ' echo "RELAYAUTH:$(postconf -h smtp_sasl_auth_enable 2>/dev/null)";'
            . ' echo "LISTEN_START";'
            . ' (ss -ltnp 2>/dev/null || netstat -ltnp 2>/dev/null) | awk \'{print $4" "$NF}\';'
            . ' echo "LISTEN_END"'
        );

        $mtaList = [];
        $imapList = [];
        $relayRaw = '';
        $relayAuth = false;
        foreach (explode("\n", $output) as $line) {
            $line = trim($line);
            if (strpos($line, 'MTA:') === 0) {
                $mtaList = self::pisahDaftar(substr($line, 4));
            } elseif (strpos($line, 'IMAP:') === 0) {
                $imapList = self::pisahDaftar(substr($line, 5));
            } elseif (strpos($line, 'RELAY:') === 0) {
                $relayRaw = trim(substr($line, 6));
            } elseif (strpos($line, 'RELAYAUTH:') === 0) {
                $relayAuth = strtolower(trim(substr($line, 10))) === 'yes';
            }
        }
        //postfix dan exim mendahului sendmail: keduanya menyediakan binari
        //sendmail sendiri, jadi sendmail hanya berarti bila ia satu-satunya
        $mta = carr::get($mtaList, 0);
        $imap = carr::get($imapList, 0);

        $listening = $this->parseListening($output);

        //hanya porta yang terikat ke antarmuka publik yang dihitung; 127.0.0.1
        //berarti MTA lokal untuk keperluan sistem, bukan layanan surel
        $publik = [];
        foreach ($listening as $port => $alamat) {
            foreach ($alamat as $a) {
                if ($this->isPublicAddress($a)) {
                    $publik[$port] = carr::get(self::$portRole, $port);

                    break;
                }
            }
        }

        $relay = self::parseRelay($relayRaw, $relayAuth);

        return [
            'relay' => $relay,
            'mta' => $mta,
            'mta_list' => $mtaList,
            'imap' => $imap,
            'imap_list' => $imapList,
            'listening' => $listening,
            'public' => $publik,
            'isMailServer' => count($publik) > 0,
        ];
    }

    /**
     * Menguji sebuah konfigurasi relai sebelum diterapkan.
     *
     * Empat langkah berurutan, dan berhenti pada kegagalan pertama: nama host
     * dapat diresolusi, portanya dapat dihubungi, TLS terbentuk, lalu
     * autentikasi SMTP diterima. Menguji sampai autentikasi itu yang membedakan
     * "konfigurasinya masuk akal" dari "konfigurasinya benar-benar bekerja" —
     * salah kunci hanya ketahuan di langkah terakhir, dan tanpa uji ini
     * kegagalannya baru muncul diam-diam saat ada surat yang gagal terkirim.
     *
     * Kredensial dikirim lewat berkas sementara bermode 600, bukan lewat
     * argumen perintah: argumen terlihat di daftar proses oleh pengguna lain
     * di server yang sama.
     *
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     *
     * @return array errCode, errMessage, steps
     */
    public function testRelay($host, $port, $username, $password) {
        $host = trim((string) $host);
        $port = (int) $port;
        $steps = [];

        if (strlen($host) == 0 || $port <= 0) {
            return ['errCode' => 1, 'errMessage' => 'Host atau porta relai belum diisi', 'steps' => $steps];
        }

        //1. resolusi nama
        $ip = trim($this->run(
            'getent hosts ' . escapeshellarg($host) . " 2>/dev/null | head -1 | awk '{print \$1}'"
        ));
        $steps[] = ['label' => 'Resolusi DNS', 'ok' => strlen($ip) > 0,
            'detail' => strlen($ip) > 0 ? $host . ' -> ' . $ip : 'nama host tidak dapat diresolusi'];
        if (strlen($ip) == 0) {
            return ['errCode' => 1, 'errMessage' => 'Nama host ' . $host . ' tidak dapat diresolusi dari server ini', 'steps' => $steps];
        }

        //2. porta terbuka
        $tcp = trim($this->run(
            'timeout 8 bash -c "</dev/tcp/' . $host . '/' . $port . '" >/dev/null 2>&1 && echo OK || echo GAGAL'
        ));
        $tcpOk = strpos($tcp, 'OK') !== false;
        $steps[] = ['label' => 'Koneksi TCP', 'ok' => $tcpOk,
            'detail' => $tcpOk ? 'porta ' . $port . ' terbuka' : 'porta ' . $port . ' tidak dapat dihubungi'];
        if (!$tcpOk) {
            return [
                'errCode' => 1,
                'errMessage' => 'Porta ' . $port . ' pada ' . $host . ' tidak dapat dihubungi dari server ini.'
                    . ' Penyedia cloud lazim memblokir porta 25 keluar — coba 587 atau 2525.',
                'steps' => $steps,
            ];
        }

        //3 & 4. TLS lalu autentikasi SMTP
        $hasil = $this->smtpAuthProbe($host, $port, $username, $password);
        $steps[] = ['label' => 'TLS', 'ok' => carr::get($hasil, 'tls'),
            'detail' => carr::get($hasil, 'tls') ? 'terbentuk' : 'gagal membentuk TLS'];
        $steps[] = ['label' => 'Autentikasi SMTP', 'ok' => carr::get($hasil, 'auth'),
            'detail' => carr::get($hasil, 'detail')];

        if (!carr::get($hasil, 'auth')) {
            return [
                'errCode' => 1,
                'errMessage' => 'Autentikasi ke relai ditolak: ' . carr::get($hasil, 'detail'),
                'steps' => $steps,
            ];
        }

        return ['errCode' => 0, 'errMessage' => '', 'steps' => $steps];
    }

    /**
     * Melakukan jabat tangan SMTP sampai AUTH LOGIN.
     *
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     *
     * @return array tls, auth, detail
     */
    protected function smtpAuthProbe($host, $port, $username, $password) {
        //porta 465 memakai TLS sejak awal; sisanya menaikkannya lewat STARTTLS
        $opsiTls = ((int) $port === 465) ? '' : ' -starttls smtp';

        $skrip = "EHLO devcloud.local\r\nAUTH LOGIN\r\n"
            . base64_encode((string) $username) . "\r\n"
            . base64_encode((string) $password) . "\r\nQUIT\r\n";

        //berkas sementara bermode 600 supaya kredensial tidak muncul di daftar proses
        $berkas = '/tmp/.devcloud-smtp-' . bin2hex(random_bytes(6));
        $perintah = 'umask 077; printf %s ' . escapeshellarg($skrip) . ' > ' . $berkas . '; '
            . 'timeout 25 openssl s_client -quiet -crlf' . $opsiTls
            . ' -connect ' . escapeshellarg($host . ':' . $port) . ' < ' . $berkas . ' 2>&1; '
            . 'rm -f ' . $berkas;

        $output = (string) $this->run($perintah);

        $tls = stripos($output, 'CONNECTED') !== false
            || stripos($output, 'verify return') !== false
            || preg_match('/^220[\s-]/m', $output) === 1;

        if (preg_match('/^235[\s-]*(.*)$/m', $output, $m)) {
            return ['tls' => true, 'auth' => true, 'detail' => trim($m[1]) ?: 'diterima'];
        }
        if (preg_match('/^(535|534|530|454)[\s-]*(.*)$/m', $output, $m)) {
            return ['tls' => $tls, 'auth' => false, 'detail' => trim($m[1] . ' ' . $m[2])];
        }
        if (stripos($output, 'openssl: not found') !== false || stripos($output, 'command not found') !== false) {
            return ['tls' => false, 'auth' => false, 'detail' => 'openssl tidak tersedia di server ini'];
        }

        return [
            'tls' => $tls,
            'auth' => false,
            'detail' => cstr::limit(trim(preg_replace('/\s+/', ' ', $output)), 160) ?: 'tidak ada jawaban dari relai',
        ];
    }

    /**
     * Menerapkan konfigurasi relai pada Postfix.
     *
     * Menguji lebih dulu dan menolak menulis apa pun bila ujinya gagal:
     * mengganti relai dengan kredensial yang salah membuat seluruh surat keluar
     * mengendap di antrean, dan itu baru ketahuan berjam-jam kemudian.
     *
     * Berkas lama dicadangkan bertanggal sebelum ditimpa, dan Postfix hanya
     * di-reload — bukan restart — supaya sambungan yang sedang berjalan tidak
     * terputus.
     *
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     * @param bool   $skipTest hanya untuk pemanggil yang sudah menguji sendiri
     *
     * @return array errCode, errMessage, steps, output
     */
    public function applyRelay($host, $port, $username, $password, $skipTest = false) {
        $steps = [];
        if (!$skipTest) {
            $uji = $this->testRelay($host, $port, $username, $password);
            $steps = carr::get($uji, 'steps', []);
            if (carr::get($uji, 'errCode') != 0) {
                return [
                    'errCode' => 1,
                    'errMessage' => carr::get($uji, 'errMessage'),
                    'steps' => $steps,
                    'output' => '',
                ];
            }
        }

        $relayValue = '[' . trim((string) $host) . ']:' . (int) $port;
        $barisKredensial = $relayValue . ' ' . $username . ':' . $password;
        $berkas = '/etc/postfix/sasl_passwd';
        $stempel = trim($this->run('date +%Y%m%d%H%M%S'));

        //umask 077 dipasang sebelum menulis: berkas ini memuat kredensial relai
        //dan tidak boleh terbaca pengguna lain walau sekejap
        $perintah = $this->sudo() . 'bash -c ' . escapeshellarg(
            'set -e; umask 077; '
            . '[ -f ' . $berkas . ' ] && cp -a ' . $berkas . ' ' . $berkas . '.bak-' . $stempel . '; '
            . 'printf %s\\n ' . escapeshellarg($barisKredensial) . ' > ' . $berkas . '; '
            . 'chmod 600 ' . $berkas . '; '
            . 'postmap ' . $berkas . '; '
            . 'postconf -e ' . escapeshellarg('relayhost = ' . $relayValue) . '; '
            . 'postconf -e ' . escapeshellarg('smtp_sasl_auth_enable = yes') . '; '
            . 'postconf -e ' . escapeshellarg('smtp_sasl_password_maps = hash:' . $berkas) . '; '
            . 'postconf -e ' . escapeshellarg('smtp_sasl_security_options = noanonymous') . '; '
            . 'postconf -e ' . escapeshellarg('smtp_tls_security_level = may') . '; '
            . 'postfix reload'
        ) . ' 2>&1; echo "exit status $?"';

        $output = (string) $this->run($perintah);

        if (strpos($output, 'exit status 0') === false) {
            return [
                'errCode' => 1,
                'errMessage' => 'Konfigurasi lulus uji tetapi gagal diterapkan. Berkas lama dicadangkan sebagai '
                    . $berkas . '.bak-' . $stempel,
                'steps' => $steps,
                'output' => $output,
            ];
        }

        //dipastikan dari sisi Postfix sendiri, bukan sekadar dari kode keluar
        $relaySekarang = trim($this->run($this->sudo() . 'postconf -h relayhost 2>/dev/null'));
        if (strpos($relaySekarang, trim((string) $host)) === false) {
            return [
                'errCode' => 1,
                'errMessage' => 'Perintah berhasil tetapi relayhost belum berubah (terbaca: ' . $relaySekarang . ')',
                'steps' => $steps,
                'output' => $output,
            ];
        }

        return ['errCode' => 0, 'errMessage' => '', 'steps' => $steps, 'output' => $output];
    }

    /**
     * Ringkas: apakah server ini melayani surel ke luar.
     *
     * @return bool
     */
    public function isMailServer() {
        $info = $this->inspect();

        return carr::get($info, 'isMailServer') === true;
    }

    /**
     * Mengurai nilai relayhost Postfix.
     *
     * Bentuknya `[host]:port`, `host:port`, atau `host` saja; kurung siku
     * berarti jangan cari MX, dan itu justru yang lazim untuk relai.
     *
     * @param string $nilai
     * @param bool   $auth
     *
     * @return null|array host, port, provider, auth, raw
     */
    protected static function parseRelay($nilai, $auth) {
        $nilai = trim((string) $nilai);
        if (strlen($nilai) == 0) {
            return null;
        }

        $host = $nilai;
        $port = null;
        if (preg_match('/^\[?([^\]:]+)\]?(?::(\d+))?$/', $nilai, $m)) {
            $host = $m[1];
            $port = isset($m[2]) ? (int) $m[2] : null;
        }

        $provider = null;
        foreach (self::$relayProvider as $petunjuk => $nama) {
            if (stripos($host, $petunjuk) !== false) {
                $provider = $nama;

                break;
            }
        }

        return [
            'host' => $host,
            'port' => $port,
            'provider' => $provider,
            'auth' => (bool) $auth,
            'raw' => $nilai,
        ];
    }

    /**
     * @param string $nilai
     *
     * @return array
     */
    protected static function pisahDaftar($nilai) {
        $hasil = [];
        foreach (explode(',', (string) $nilai) as $bagian) {
            $bagian = trim($bagian);
            if (strlen($bagian) > 0) {
                $hasil[] = $bagian;
            }
        }

        return $hasil;
    }

    /**
     * @param string $output
     *
     * @return array porta => daftar alamat
     */
    protected function parseListening($output) {
        $mulai = strpos($output, 'LISTEN_START');
        $akhir = strpos($output, 'LISTEN_END');
        if ($mulai === false || $akhir === false) {
            return [];
        }
        $blok = substr($output, $mulai + strlen('LISTEN_START'), $akhir - $mulai - strlen('LISTEN_START'));

        $hasil = [];
        foreach (explode("\n", $blok) as $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            $alamat = trim((string) carr::get(explode(' ', $line), 0));
            //bentuknya 0.0.0.0:25, [::]:25, atau *:25
            $pos = strrpos($alamat, ':');
            if ($pos === false) {
                continue;
            }
            $port = (int) substr($alamat, $pos + 1);
            if (!isset(self::$portRole[$port])) {
                continue;
            }
            $host = substr($alamat, 0, $pos);
            if (!isset($hasil[$port])) {
                $hasil[$port] = [];
            }
            if (!in_array($host, $hasil[$port])) {
                $hasil[$port][] = $host;
            }
        }

        return $hasil;
    }

    /**
     * @param string $alamat
     *
     * @return bool
     */
    protected function isPublicAddress($alamat) {
        $alamat = trim($alamat, '[]');

        return !in_array($alamat, ['127.0.0.1', '::1', 'localhost'], true);
    }
}
