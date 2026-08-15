<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Jembatan antara sebuah server dan `CVendor_LiteSpeed`.
 *
 * Pembagian tugasnya disengaja dan sebaiknya tetap begitu:
 *
 * - `CVendor_LiteSpeed` memodelkan **format konfigurasi** LiteSpeed. Ia murni —
 *   teks masuk, pohon keluar — sehingga dapat diuji tanpa satu pun mesin
 * - kelas ini yang **berbicara ke mesin**: menjalankan perintah lewat SSH,
 *   menyelesaikan jalur berkas, dan menyerahkan hasilnya untuk diurai
 *
 * Menggabung keduanya akan membuat penguraian konfigurasi mustahil diuji tanpa
 * server, dan itu kerugian yang tidak sepadan.
 */
class CServer_WebServer_LiteSpeed {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * Cache konfigurasi utama selama satu permintaan.
     *
     * Tiap perjalanan SSH berbiaya mahal dan `httpd_config.conf` dibaca oleh
     * hampir setiap operasi di sini — daftar vhost, pencarian satu vhost, dan
     * daftar listener semuanya berangkat dari berkas yang sama.
     *
     * @var null|CVendor_LiteSpeed_Data
     */
    protected $httpdConfig;

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
     * Membaca berkas di server, dengan `sudo -n` sebagai cadangan.
     *
     * `/usr/local/lsws/conf` hanya terbaca root pada pemasangan bawaan,
     * sementara devcloud kerap menyambung sebagai pengguna biasa. Keadaan
     * "tidak terbaca" dibedakan dari "tidak ada" karena keduanya menuntut
     * tindakan yang sama sekali berbeda — yang satu soal hak akses, yang lain
     * soal konfigurasi yang memang tidak ada.
     *
     * @param string $path
     *
     * @return array ['state' => ok|denied|missing, 'content' => string]
     */
    public function readFile($path) {
        $quoted = escapeshellarg($path);
        $output = $this->server->runCommand(
            //`[ -e ]` bernilai salah bila direktori induknya sendiri tidak
            //dapat ditembus, dan itu keadaan yang lazim: /usr/local/lsws/conf
            //ber-mode 0750 milik lsadm. Ketika ia menjadi penjaga bagi jalur
            //sudo, berkas yang sebenarnya terbaca lewat sudo dilaporkan
            //`missing` - tanpa galat, seolah tidak ada apa-apa di sana. Karena
            //itu sudo dicoba lebih dulu, dan keberadaan berkas baru
            //dipertanyakan sesudah keduanya gagal.
            'if [ -r ' . $quoted . ' ]; then echo "LSSTATE|ok"; cat ' . $quoted . ';'
            . ' elif sudo -n cat ' . $quoted . ' >/dev/null 2>&1; then echo "LSSTATE|ok"; sudo -n cat ' . $quoted . ';'
            . ' elif [ -e ' . $quoted . ' ] || sudo -n test -e ' . $quoted . ' 2>/dev/null; then echo "LSSTATE|denied";'
            . ' else echo "LSSTATE|missing"; fi'
        );

        $line = preg_split('/\r\n|\r|\n/', (string) $output, 2);
        $state = trim((string) carr::get($line, 0));
        $map = ['LSSTATE|ok' => 'ok', 'LSSTATE|denied' => 'denied', 'LSSTATE|missing' => 'missing'];

        return [
            'state' => carr::get($map, $state, 'missing'),
            'content' => $state == 'LSSTATE|ok' ? (string) carr::get($line, 1, '') : '',
        ];
    }

    /**
     * Menulis berkas di server, dengan cadangan bertanggal lebih dulu.
     *
     * Isinya dikirim sebagai base64 alih-alih ditempel ke dalam perintah.
     * Konfigurasi LiteSpeed memuat kutip, `$`, backtick, dan tanda kurung —
     * seluruhnya berarti bagi shell — sehingga menempelkannya, betapapun
     * hati-hati mengutipnya, adalah cara paling langsung merusak berkas yang
     * sedang diperbaiki.
     *
     * Cadangannya dibuat **sebelum** menulis dan namanya memuat waktu, jadi
     * beberapa kali penerapan tidak saling menimpa.
     *
     * @param string $path
     * @param string $content
     *
     * @return array ['ok' => bool, 'backup' => string, 'output' => string]
     */
    public function writeFile($path, $content) {
        $file = escapeshellarg($path);
        $backup = $path . '.bak.' . date('YmdHis');
        $backupArg = escapeshellarg($backup);
        $encoded = base64_encode((string) $content);

        //`sudo -n` dipakai sebagai cadangan pada tiap langkah, karena berkas
        //konfigurasinya lazim hanya dapat ditulis root
        $script = 'set -e; '
            . 'cp -p ' . $file . ' ' . $backupArg . ' 2>/dev/null || sudo -n cp -p ' . $file . ' ' . $backupArg . '; '
            . "printf '%s' " . escapeshellarg($encoded) . ' | base64 -d > /tmp/lsvh.$$ ; '
            . 'cat /tmp/lsvh.$$ > ' . $file . ' 2>/dev/null || sudo -n cp /tmp/lsvh.$$ ' . $file . '; '
            . 'rm -f /tmp/lsvh.$$; '
            . 'echo "LSWRITE|ok"';

        $output = (string) $this->server->runCommand($script);

        return [
            'ok' => strpos($output, 'LSWRITE|ok') !== false,
            'backup' => $backup,
            'output' => $output,
        ];
    }

    /**
     * Memuat ulang LiteSpeed tanpa memutus koneksi yang sedang berjalan.
     *
     * @return string
     */
    public function restart() {
        return (string) $this->server->runCommand(
            CVendor_LiteSpeed::serverRoot() . '/bin/lswsctrl restart 2>&1'
            . ' || sudo -n ' . CVendor_LiteSpeed::serverRoot() . '/bin/lswsctrl restart 2>&1'
        );
    }

    /**
     * Konfigurasi utama server (`httpd_config.conf`).
     *
     * @return null|CVendor_LiteSpeed_Data
     */
    public function getHttpdConfig() {
        if ($this->httpdConfig === null) {
            $file = $this->readFile(CVendor_LiteSpeed::serverRoot() . '/conf/httpd_config.conf');
            if (carr::get($file, 'state') != 'ok') {
                return null;
            }
            $this->httpdConfig = CVendor_LiteSpeed::parseConf(
                CVendor_LiteSpeed_Info::CT_SERV,
                carr::get($file, 'content')
            );
        }

        return $this->httpdConfig;
    }

    /**
     * Daftar virtual host menurut konfigurasi, bukan menurut isi direktori.
     *
     * Bedanya nyata: `conf/vhosts/*` memuat juga vhost yang sudah dicabut dari
     * konfigurasi, sekaligus melewatkan vhost yang `configFile`-nya menunjuk ke
     * luar direktori itu — bentuk yang lazim pada pemasangan yang diatur panel.
     *
     * @return array[] masing-masing ['name', 'vhRoot', 'configFile', 'configPath']
     */
    public function getVirtualHostList() {
        $config = $this->getHttpdConfig();
        if ($config == null) {
            return [];
        }

        $list = [];
        foreach ($config->getRootNode()->getChildList('virtualhost') as $node) {
            $vhRoot = (string) $node->getChildVal('vhRoot');
            $configFile = (string) $node->getChildVal('configFile');
            $list[] = [
                'name' => (string) $node->getVal(),
                'vhRoot' => $vhRoot,
                'configFile' => $configFile,
                'configPath' => CVendor_LiteSpeed::expandPath($configFile, $vhRoot),
            ];
        }

        return $list;
    }

    /**
     * Daftar virtual host beserta domain dan document root-nya.
     *
     * Kedua nilai itu tidak ada di `httpd_config.conf`; ia tersimpan di berkas
     * masing-masing vhost. Mengambilnya satu per satu berarti satu perjalanan
     * SSH per vhost, jadi seluruhnya dibaca dalam **satu** perintah — sama
     * seperti `inspect()` melakukannya untuk daftar web server.
     *
     * @return array[] masing-masing ditambah 'domain' dan 'root'
     */
    public function getVirtualHostSummaryList() {
        $list = $this->getVirtualHostList();
        if (count($list) == 0) {
            return [];
        }

        $script = '';
        foreach ($list as $index => $item) {
            $path = (string) carr::get($item, 'configPath');
            if (strlen($path) == 0) {
                continue;
            }
            $quoted = escapeshellarg($path);
            $script .= 'c=$(cat ' . $quoted . ' 2>/dev/null || sudo -n cat ' . $quoted . ' 2>/dev/null); '
                . 'echo "VH|' . (int) $index . '|'
                . '$(printf "%s" "$c" | grep -m1 "^vhDomain" | awk \'{print $2}\')|'
                . '$(printf "%s" "$c" | grep -m1 "^docRoot" | awk \'{print $2}\')"; ';
        }
        if (strlen($script) == 0) {
            return $list;
        }

        $output = $this->server->runCommand($script);
        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (substr($line, 0, 3) != 'VH|') {
                continue;
            }
            $part = explode('|', $line);
            $index = (int) carr::get($part, 1);
            if (!isset($list[$index])) {
                continue;
            }
            $list[$index]['domain'] = trim((string) carr::get($part, 2)) ?: '-';
            $list[$index]['root'] = trim((string) carr::get($part, 3)) ?: '-';
        }

        foreach ($list as &$item) {
            $item['domain'] = carr::get($item, 'domain', '-');
            $item['root'] = carr::get($item, 'root', '-');
        }
        unset($item);

        return $list;
    }

    /**
     * Satu virtual host beserta konfigurasinya yang sudah terurai.
     *
     * @param string $name
     *
     * @return null|array ['name','vhRoot','configPath','state','raw','data']
     */
    public function getVirtualHost($name) {
        $entry = null;
        foreach ($this->getVirtualHostList() as $item) {
            if (carr::get($item, 'name') === (string) $name) {
                $entry = $item;

                break;
            }
        }
        if ($entry == null) {
            return null;
        }

        $path = (string) carr::get($entry, 'configPath');
        if (strlen($path) == 0) {
            return carr::merge($entry, ['state' => 'missing', 'raw' => '', 'data' => null]);
        }

        $file = $this->readFile($path);
        $state = (string) carr::get($file, 'state');
        $raw = (string) carr::get($file, 'content');

        return carr::merge($entry, [
            'state' => $state,
            'raw' => $raw,
            'data' => $state == 'ok'
                ? CVendor_LiteSpeed::parseConf(CVendor_LiteSpeed_Info::CT_VH, $raw)
                : null,
        ]);
    }

    /**
     * Listener beserta pemetaannya ke virtual host.
     *
     * Berguna berdampingan dengan detail vhost: yang menentukan sebuah vhost
     * terjangkau atau tidak adalah ada-tidaknya `map` yang menunjuk ke sana,
     * dan itu tidak tertulis di berkas vhost-nya sendiri.
     *
     * @return array[]
     */
    public function getListenerList() {
        $config = $this->getHttpdConfig();
        if ($config == null) {
            return [];
        }

        $list = [];
        foreach ($config->getRootNode()->getChildList('listener') as $node) {
            //`CVendor_LiteSpeed_Data::afterRead()` memekarkan tiap baris
            //`map <vhost> <domain, domain>` menjadi simpul `vhmap` beranak
            //`vhost` dan `domain`, lalu **membuang** simpul `map` aslinya —
            //jadi mencari `map` di sini selalu berakhir kosong. Bentuk `map`
            //tetap dicoba sebagai cadangan untuk konfigurasi yang belum
            //melewati afterRead().
            $map = [];
            foreach ($node->getChildList('vhmap') as $mapNode) {
                $map[] = [
                    'vhost' => (string) $mapNode->getChildVal('vhost') ?: (string) $mapNode->getVal(),
                    'domain' => (string) $mapNode->getChildVal('domain'),
                ];
            }
            foreach ($node->getChildList('map') as $mapNode) {
                $value = (string) $mapNode->getVal();
                $position = strpos($value, ' ');
                $map[] = [
                    'vhost' => $position === false ? $value : substr($value, 0, $position),
                    'domain' => $position === false ? '' : trim(substr($value, $position + 1)),
                ];
            }

            //`address` berbentuk `ip:port`. Pengurai juga memekarkannya menjadi
            //anak `ip` dan `port` tersendiri, dan yang itu didahulukan karena
            //sudah dinormalkan olehnya — `*` menjadi `ANY`. Pemisahan manual
            //hanya dipakai bila keduanya tidak ada.
            $address = (string) $node->getChildVal('address');
            $ip = (string) $node->getChildVal('ip');
            $port = (string) $node->getChildVal('port');
            if (strlen($ip) == 0 && strlen($port) == 0) {
                $position = strrpos($address, ':');
                $ip = $position === false ? $address : substr($address, 0, $position);
                $port = $position === false ? '' : substr($address, $position + 1);
            }

            $list[] = [
                'name' => (string) $node->getVal(),
                'address' => $address,
                'ip' => $ip,
                'port' => $port,
                'secure' => (string) $node->getChildVal('secure'),
                'keyFile' => (string) $node->getChildVal('keyFile'),
                'certFile' => (string) $node->getChildVal('certFile'),
                'map' => $map,
            ];
        }

        return $list;
    }

    /**
     * Aplikasi eksternal tingkat server — penerjemah PHP dan kawan-kawannya.
     *
     * Inilah yang sesungguhnya menyambungkan sebuah vhost ke penerjemah PHP,
     * jadi ia menjawab pertanyaan yang tidak dijawab oleh daftar lsphp yang
     * terpasang: mana yang benar-benar terpakai.
     *
     * @return array[]
     */
    public function getExternalAppList() {
        $config = $this->getHttpdConfig();
        if ($config == null) {
            return [];
        }

        $list = [];
        foreach ($config->getRootNode()->getChildList('extprocessor') as $node) {
            $list[] = [
                'name' => (string) $node->getVal(),
                'type' => (string) $node->getChildVal('type'),
                'address' => (string) $node->getChildVal('address'),
                'path' => (string) $node->getChildVal('path'),
                'maxConns' => (string) $node->getChildVal('maxConns'),
                'instances' => (string) $node->getChildVal('instances'),
            ];
        }

        return $list;
    }

    /**
     * Penerjemah PHP yang terpasang, beserta versinya.
     *
     * Dibaca dari isi direktori dan bukan dari daftar versi yang diketahui
     * kode, karena daftar semacam itu menua: LiteSpeed merilis lsphp baru dan
     * daftar yang ditulis tangan akan melaporkan versi yang benar-benar
     * terpasang sebagai "tidak ada".
     *
     * Versinya dipanggil dari binernya sendiri — nama direktori hanya menyebut
     * versi mayor-minor, sementara yang menentukan saat menelusuri masalah
     * justru versi tambalannya.
     *
     * @return array[] masing-masing ['name', 'version', 'binary']
     */
    public function getPhpList() {
        $root = CVendor_LiteSpeed::serverRoot();
        $output = $this->server->runCommand(
            'for d in ' . $root . '/lsphp*/; do '
            . '  [ -d "$d" ] || continue; '
            . '  d=${d%/}; '
            . '  n=$(basename "$d"); '
            . '  b=""; '
            . '  for c in "$d/bin/php" "$d/bin/lsphp"; do [ -x "$c" ] && b="$c" && break; done; '
            . '  v=""; [ -n "$b" ] && v=$("$b" -v 2>/dev/null | head -1); '
            . '  echo "LSPHP|$n|$b|$v"; '
            . 'done'
        );

        $list = [];
        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (substr($line, 0, 6) != 'LSPHP|') {
                continue;
            }
            $part = explode('|', $line, 4);
            $list[] = [
                'name' => trim((string) carr::get($part, 1)),
                'binary' => trim((string) carr::get($part, 2)),
                'version' => trim((string) carr::get($part, 3)),
            ];
        }

        return $list;
    }

    /**
     * Listener yang memetakan ke sebuah virtual host.
     *
     * @return array[]
     */
    /**
     * Versi lsphp yang dikenali, terbaru lebih dulu.
     *
     * @return array
     */
    public static function supportedPhpVersionList() {
        return [
            'lsphp85', 'lsphp84', 'lsphp83', 'lsphp82', 'lsphp81', 'lsphp80',
            'lsphp74', 'lsphp73', 'lsphp72', 'lsphp71', 'lsphp70', 'lsphp56',
        ];
    }

    /**
     * Versi yang dikenali digabung dengan yang benar-benar terpasang.
     *
     * Yang terpasang tetapi tidak ada dalam daftar ikut disertakan — mesin yang
     * sebenarnya lebih berhak menentukan daripada daftar di dalam kode.
     *
     * @return array tiap entri: name, binary, version, installed
     */
    public function getPhpAvailabilityList() {
        $installed = [];
        foreach ($this->getPhpList() as $item) {
            $installed[(string) carr::get($item, 'name')] = $item;
        }

        $nameList = static::supportedPhpVersionList();
        foreach (array_keys($installed) as $name) {
            if (!in_array($name, $nameList)) {
                $nameList[] = $name;
            }
        }
        rsort($nameList);

        $list = [];
        foreach ($nameList as $name) {
            $item = carr::get($installed, $name, []);
            $list[] = [
                'name' => $name,
                'binary' => (string) carr::get($item, 'binary'),
                'version' => (string) carr::get($item, 'version'),
                'installed' => array_key_exists($name, $installed) ? 1 : 0,
            ];
        }

        return $list;
    }

    /**
     * Nomor versi dari 'lsphp82' maupun '82'.
     *
     * @param string $version
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    protected static function normalizePhpVersion($version) {
        $number = str_replace('lsphp', '', (string) $version);
        if (!preg_match('/^\d{2}$/', $number)) {
            throw new InvalidArgumentException('Versi lsphp tidak sah: ' . $version);
        }

        return $number;
    }

    /**
     * Daftar paket lsphp beserta ekstensinya untuk satu keluarga distribusi.
     *
     * Namanya berbeda antar distribusi, bukan sekadar kata kerja pemasangnya:
     * `mysqlnd` pada RHEL adalah `mysql` pada Debian, dan `pecl-redis` adalah
     * `redis`. Beberapa yang ada di RHEL — `process`, `json`, `devel` — tidak
     * dipaketkan terpisah di Debian.
     *
     * @param string $version boleh 'lsphp82' maupun '82'
     * @param string $family  hasil CServer_OSAbstract::getFamily()
     *
     * @throws InvalidArgumentException
     *
     * @return string paket yang dipisah spasi
     */
    public static function phpPackageList($version, $family) {
        $number = static::normalizePhpVersion($version);

        if ($family == 'debian') {
            $extensionList = [
                'common', 'mysql', 'opcache', 'curl', 'intl', 'imagick',
                'redis', 'pgsql', 'sqlite3', 'igbinary', 'msgpack', 'memcached',
            ];
        } else {
            $extensionList = [
                'common', 'mysqlnd', 'process', 'gd', 'mbstring', 'opcache',
                'bcmath', 'pdo', 'xml', 'json', 'pecl-redis', 'devel',
            ];
            if ((int) $number < 80) {
                $extensionList[] = 'mcrypt';
            }
            if ((int) $number >= 74) {
                $extensionList[] = 'redis';
                $extensionList[] = 'pdo_pgsql';
            }
        }

        $packageList = ['lsphp' . $number];
        foreach ($extensionList as $extension) {
            $packageList[] = 'lsphp' . $number . '-' . $extension;
        }

        return implode(' ', $packageList);
    }

    /**
     * Pasang satu versi lsphp, memakai manajer paket distribusinya sendiri.
     *
     * @param string $version
     *
     * @throws InvalidArgumentException
     *
     * @return string keluaran perintahnya
     */
    public function installPhp($version) {
        $distro = $this->server->distro();
        $package = static::phpPackageList($version, $distro->getFamily());

        $output = '';
        foreach ($distro->getInstallCommand($package) as $command) {
            $output .= (string) $this->server->runCommand('sudo ' . $command) . PHP_EOL;
        }

        return $output;
    }

    public function getListenerListForVirtualHost($name) {
        $list = [];
        foreach ($this->getListenerList() as $listener) {
            foreach (carr::get($listener, 'map', []) as $map) {
                if ((string) carr::get($map, 'vhost') === (string) $name) {
                    $listener['domain'] = (string) carr::get($map, 'domain');
                    $list[] = $listener;

                    break;
                }
            }
        }

        return $list;
    }
}
