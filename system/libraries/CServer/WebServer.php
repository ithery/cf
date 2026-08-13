<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Pemeriksa web server pada sebuah server.
 *
 * Satu mesin bisa memasang lebih dari satu web server sekaligus — misalnya
 * LiteSpeed memegang port 80/443 sementara nginx tetap terpasang dan berjalan
 * di belakang. Karena itu pemeriksaannya mengembalikan daftar, bukan satu
 * jawaban tunggal, dan port yang benar-benar didengarkan ikut dilaporkan agar
 * terlihat siapa yang sesungguhnya melayani permintaan.
 */
class CServer_WebServer {
    const TYPE_LITESPEED = 'litespeed';

    const TYPE_OPENLITESPEED = 'openlitespeed';

    const TYPE_NGINX = 'nginx';

    const TYPE_APACHE = 'apache';

    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var null|CServer_WebServer_LiteSpeed
     */
    protected $liteSpeed;

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
     * Seluruh web server yang terpasang beserta keadaannya.
     *
     * Dikumpulkan dalam satu perintah karena tiap perjalanan SSH berbiaya
     * mahal, lalu diurai per baris berawalan.
     *
     * @return array kunci tipe => detail
     */
    public function inspect() {
        $script = ''
            //Sebagian server hanya mengizinkan masuk sebagai pengguna biasa
            //yang ber-sudo, sementara direktori konfigurasi webserver tidak
            //dapat ditembusnya - `/usr/local/lsws/conf` lazimnya hanya milik
            //root. Tanpa cadangan `sudo -n`, pemeriksaan direktorinya gagal
            //diam-diam: tidak ada galat, hanya vhost yang selalu kosong.
            //Pola cadangan ini sama dengan yang sudah dipakai readFile().
            . '_isdir() { [ -d "$1" ] || sudo -n test -d "$1" 2>/dev/null; }; '
            . '_isfile() { [ -f "$1" ] || sudo -n test -f "$1" 2>/dev/null; }; '
            . '_count() { n=$(ls -1 "$1" 2>/dev/null | wc -l); '
            . '  [ "$n" -eq 0 ] && n=$(sudo -n ls -1 "$1" 2>/dev/null | wc -l); echo "$n"; }; '
            //status layanan; nama unit berbeda antar distribusi
            . 'for u in lsws lshttpd openlitespeed nginx httpd apache2; do '
            . '  s=$(systemctl is-active $u 2>/dev/null); [ -n "$s" ] && echo "SVC|$u|$s"; '
            . 'done; '
            //versi
            . 'for b in /usr/local/lsws/bin/lshttpd /usr/local/lsws/bin/openlitespeed; do '
            . '  [ -x "$b" ] && echo "VER|litespeed|$("$b" -v 2>&1 | head -1)"; '
            . 'done; '
            . 'command -v nginx >/dev/null 2>&1 && echo "VER|nginx|$(nginx -v 2>&1)"; '
            . 'command -v httpd >/dev/null 2>&1 && echo "VER|apache|$(httpd -v 2>&1 | head -1)"; '
            . 'command -v apache2 >/dev/null 2>&1 && echo "VER|apache|$(apache2 -v 2>&1 | head -1)"; '
            //Port yang benar-benar didengarkan, beserta proses pemiliknya.
            //Nama proses hanya terlihat untuk proses milik sendiri, sedangkan
            //webserver berjalan sebagai root - tanpa sudo kolomnya kosong dan
            //port tidak pernah tercocokkan ke tipe webservernya.
            . '(sudo -n ss -lntp 2>/dev/null || ss -lntp 2>/dev/null) | awk \'NR>1 {print "PORT|" $4 "|" $6}\'; '
            //konfigurasi dan jumlah vhost
            . '_isdir /usr/local/lsws/conf && echo "CONF|litespeed|/usr/local/lsws/conf/httpd_config.conf"; '
            . '_isdir /usr/local/lsws/conf/vhosts && echo "VHOST|litespeed|$(_count /usr/local/lsws/conf/vhosts)"; '
            . '_isfile /etc/nginx/nginx.conf && echo "CONF|nginx|/etc/nginx/nginx.conf"; '
            . '_isdir /etc/nginx/sites-enabled && echo "VHOST|nginx|$(_count /etc/nginx/sites-enabled)"; '
            . '_isdir /etc/nginx/conf.d && echo "VHOSTD|nginx|$(ls -1 /etc/nginx/conf.d/*.conf 2>/dev/null | wc -l)"; '
            . '_isfile /etc/httpd/conf/httpd.conf && echo "CONF|apache|/etc/httpd/conf/httpd.conf"; '
            . '_isfile /etc/apache2/apache2.conf && echo "CONF|apache|/etc/apache2/apache2.conf"';

        $output = $this->run($script);

        $service = [];
        $result = [];
        $port = [];

        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (strlen($line) == 0 || strpos($line, '|') === false) {
                continue;
            }
            $part = explode('|', $line);
            $tag = carr::get($part, 0);

            if ($tag == 'SVC') {
                $service[carr::get($part, 1)] = carr::get($part, 2);
            } elseif ($tag == 'PORT') {
                $port[] = ['listen' => carr::get($part, 1), 'process' => carr::get($part, 2)];
            } elseif (in_array($tag, ['VER', 'CONF', 'VHOST', 'VHOSTD'])) {
                $type = carr::get($part, 1);
                if (!isset($result[$type])) {
                    $result[$type] = ['type' => $type, 'version' => null, 'config' => null, 'vhost' => null, 'status' => null, 'port' => []];
                }
                $value = carr::get($part, 2);
                if ($tag == 'VER') {
                    $result[$type]['version'] = $this->parseVersion($value);
                } elseif ($tag == 'CONF') {
                    $result[$type]['config'] = $value;
                } elseif ($tag == 'VHOST') {
                    $result[$type]['vhost'] = (int) $value;
                } elseif ($tag == 'VHOSTD') {
                    $result[$type]['vhost'] = (int) $result[$type]['vhost'] + (int) $value;
                }
            }
        }

        //status layanan dipetakan ke tipe; litespeed punya beberapa nama unit
        $unitMap = [
            'lsws' => self::TYPE_LITESPEED,
            'lshttpd' => self::TYPE_LITESPEED,
            'openlitespeed' => self::TYPE_LITESPEED,
            'nginx' => self::TYPE_NGINX,
            'httpd' => self::TYPE_APACHE,
            'apache2' => self::TYPE_APACHE,
        ];
        foreach ($service as $unit => $status) {
            $type = carr::get($unitMap, $unit);
            if ($type === null || !isset($result[$type])) {
                continue;
            }
            //aktif menang atas tidak aktif, karena satu tipe bisa punya beberapa unit
            if ($result[$type]['status'] != 'active') {
                $result[$type]['status'] = $status;
            }
            $result[$type]['unit'][] = $unit . ' (' . $status . ')';
        }

        //port dicocokkan ke tipe lewat nama proses pemiliknya
        foreach ($port as $p) {
            foreach ($result as $type => $detail) {
                $needle = $type == self::TYPE_LITESPEED ? 'litespeed' : $type;
                if (stripos($p['process'], $needle) !== false) {
                    $result[$type]['port'][] = $p['listen'];
                }
            }
        }
        foreach ($result as $type => $detail) {
            $result[$type]['port'] = array_values(array_unique($detail['port']));
            sort($result[$type]['port']);
        }

        return $result;
    }

    /**
     * Mengambil nomor versi dari baris keluaran yang bentuknya berbeda-beda:
     * "LiteSpeed/1.9.1 Open", "nginx version: nginx/1.24.0", "Server version:
     * Apache/2.4.52".
     *
     * @param string $raw
     *
     * @return string
     */
    protected function parseVersion($raw) {
        if (preg_match('#/(\d+[\d.]*)#', (string) $raw, $m)) {
            return $m[1];
        }

        return trim((string) $raw);
    }

    /**
     * Uji konfigurasi. Hanya nginx dan apache yang menyediakannya; LiteSpeed
     * memvalidasi saat memuat ulang.
     *
     * @param string $type
     *
     * @return null|string null bila tipe tidak mendukung
     */
    public function testConfig($type) {
        if ($type == self::TYPE_NGINX) {
            return $this->run('nginx -t 2>&1');
        }
        if ($type == self::TYPE_APACHE) {
            return $this->run('(command -v apachectl >/dev/null 2>&1 && apachectl configtest 2>&1)'
                . ' || (command -v httpd >/dev/null 2>&1 && httpd -t 2>&1)');
        }

        return null;
    }

    /**
     * Muat ulang konfigurasi tanpa memutus koneksi yang sedang berjalan.
     *
     * Sengaja tidak menyediakan restart: memuat ulang sudah cukup untuk
     * perubahan konfigurasi, sedangkan restart memutus layanan.
     *
     * @param string $type
     *
     * @return string
     */
    public function reload($type) {
        $command = [
            self::TYPE_LITESPEED => '/usr/local/lsws/bin/lswsctrl restart 2>&1',
            self::TYPE_NGINX => 'nginx -t 2>&1 && systemctl reload nginx 2>&1 && echo "nginx dimuat ulang"',
            self::TYPE_APACHE => '(systemctl reload httpd 2>&1 || systemctl reload apache2 2>&1)',
        ];

        return $this->run(carr::get($command, $type, 'echo "tipe tidak dikenal: ' . $type . '"'));
    }

    /**
     * Daftar berkas vhost atau server block.
     *
     * @param string $type
     *
     * @return array
     */
    public function getVirtualHostList($type) {
        if ($type == self::TYPE_LITESPEED) {
            //dibaca dari httpd_config.conf, bukan dari isi direktori: `conf/vhosts/*`
            //memuat juga vhost yang sudah dicabut dari konfigurasi, sekaligus
            //melewatkan yang berkas konfigurasinya berada di luar direktori itu
            return $this->liteSpeed()->getVirtualHostSummaryList();
        }

        if ($type == self::TYPE_NGINX) {
            $output = $this->run('for f in /etc/nginx/sites-enabled/* /etc/nginx/conf.d/*.conf; do '
                . '  [ -f "$f" ] || continue; '
                . '  n=$(basename "$f"); '
                . '  dom=$(grep -m1 -E "^\\s*server_name" "$f" 2>/dev/null | sed -E "s/^\\s*server_name\\s+//; s/;.*//"); '
                . '  root=$(grep -m1 -E "^\\s*root" "$f" 2>/dev/null | sed -E "s/^\\s*root\\s+//; s/;.*//"); '
                . '  echo "$n|$dom|$root"; '
                . 'done');
        } else {
            return [];
        }

        $list = [];
        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (strlen($line) == 0 || strpos($line, '|') === false) {
                continue;
            }
            $part = explode('|', $line);
            $list[] = [
                'name' => trim((string) carr::get($part, 0)),
                'domain' => trim((string) carr::get($part, 1)) ?: '-',
                'root' => trim((string) carr::get($part, 2)) ?: '-',
            ];
        }

        return $list;
    }

    /**
     * Nama vhost yang aman dipakai menyusun jalur berkas.
     *
     * Namanya datang dari segmen URL, sedangkan ia dipakai membentuk perintah
     * shell — jadi penyaringannya daftar-putih, bukan daftar-hitam. Titik
     * diizinkan karena nama vhost lazimnya sebuah domain, tetapi `..` ditolak
     * tersendiri supaya izin itu tidak berubah menjadi jalan naik direktori.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function isValidVirtualHostName($name) {
        $name = (string) $name;
        if (strlen($name) == 0 || strlen($name) > 255) {
            return false;
        }
        if (strpos($name, '..') !== false) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z0-9._-]+$/', $name);
    }

    /**
     * Pembantu khusus LiteSpeed.
     *
     * @return CServer_WebServer_LiteSpeed
     */
    public function liteSpeed() {
        if ($this->liteSpeed === null) {
            $this->liteSpeed = new CServer_WebServer_LiteSpeed($this->server);
        }

        return $this->liteSpeed;
    }

    /**
     * Konfigurasi satu virtual host, mentah sekaligus terurai.
     *
     * Untuk LiteSpeed seluruhnya diserahkan ke `CServer_WebServer_LiteSpeed`,
     * yang menemukan `configFile` sesungguhnya lewat `httpd_config.conf`
     * alih-alih menebaknya dari nama vhost. Bedanya bukan kerapian: vhost yang
     * berkas konfigurasinya berada di luar `conf/vhosts/<nama>/` — lazim pada
     * pemasangan yang diatur panel — tidak akan pernah ketemu bila ditebak.
     *
     * @param string $type
     * @param string $name
     *
     * @return null|array
     */
    public function getVirtualHost($type, $name) {
        if (!self::isValidVirtualHostName($name)) {
            return null;
        }

        if ($type == self::TYPE_LITESPEED) {
            $vhost = $this->liteSpeed()->getVirtualHost($name);
            if ($vhost == null) {
                return null;
            }

            return carr::merge($vhost, [
                'type' => (string) $type,
                'path' => (string) carr::get($vhost, 'configPath'),
                'readable' => carr::get($vhost, 'state') == 'ok',
            ]);
        }

        if ($type != self::TYPE_NGINX) {
            return null;
        }

        $path = '/etc/nginx/sites-enabled/' . $name;
        $file = (new CServer_WebServer_LiteSpeed($this->server))->readFile($path);
        $state = (string) carr::get($file, 'state');
        if ($state == 'missing') {
            return null;
        }

        return [
            'name' => (string) $name,
            'type' => (string) $type,
            'path' => $path,
            'state' => $state,
            'readable' => $state == 'ok',
            'raw' => (string) carr::get($file, 'content'),
            'data' => null,
        ];
    }
}
