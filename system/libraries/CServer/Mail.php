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
        $output = (string) $this->run(
            'echo "MTA:$(command -v postfix >/dev/null 2>&1 && echo postfix)'
            . '$(command -v exim >/dev/null 2>&1 && echo exim)'
            . '$(command -v sendmail >/dev/null 2>&1 && echo sendmail)";'
            . ' echo "IMAP:$(command -v dovecot >/dev/null 2>&1 && echo dovecot)'
            . '$(command -v cyrus-master >/dev/null 2>&1 && echo cyrus)";'
            //ss lebih umum tersedia daripada netstat pada distribusi baru,
            //tetapi netstat masih ada di yang lama — dua-duanya dicoba
            . ' echo "LISTEN_START";'
            . ' (ss -ltnp 2>/dev/null || netstat -ltnp 2>/dev/null) | awk \'{print $4" "$NF}\';'
            . ' echo "LISTEN_END"'
        );

        $mta = null;
        $imap = null;
        foreach (explode("\n", $output) as $line) {
            $line = trim($line);
            if (strpos($line, 'MTA:') === 0) {
                $mta = trim(substr($line, 4)) ?: null;
            } elseif (strpos($line, 'IMAP:') === 0) {
                $imap = trim(substr($line, 5)) ?: null;
            }
        }

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

        return [
            'mta' => $mta,
            'imap' => $imap,
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
