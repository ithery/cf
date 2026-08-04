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
            'if [ -r ' . $quoted . ' ]; then echo "LSSTATE|ok"; cat ' . $quoted . ';'
            . ' elif [ -e ' . $quoted . ' ]; then'
            . '   if sudo -n cat ' . $quoted . ' >/dev/null 2>&1; then echo "LSSTATE|ok"; sudo -n cat ' . $quoted . ';'
            . '   else echo "LSSTATE|denied"; fi;'
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
            $map = [];
            foreach ($node->getChildList('map') as $mapNode) {
                $map[] = (string) $mapNode->getVal();
            }
            $list[] = [
                'name' => (string) $node->getVal(),
                'address' => (string) $node->getChildVal('address'),
                'secure' => (string) $node->getChildVal('secure'),
                'map' => $map,
            ];
        }

        return $list;
    }

    /**
     * Listener yang memetakan ke sebuah virtual host.
     *
     * @param string $name
     *
     * @return array[]
     */
    public function getListenerListForVirtualHost($name) {
        $list = [];
        foreach ($this->getListenerList() as $listener) {
            foreach (carr::get($listener, 'map', []) as $map) {
                //bentuknya `namaVhost domain1, domain2`
                $part = preg_split('/\s+/', trim((string) $map), 2);
                if ((string) carr::get($part, 0) === (string) $name) {
                    $listener['domain'] = trim((string) carr::get($part, 1, ''));
                    $list[] = $listener;

                    break;
                }
            }
        }

        return $list;
    }
}
