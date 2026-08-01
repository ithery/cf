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
