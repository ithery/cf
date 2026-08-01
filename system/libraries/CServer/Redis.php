<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Pemeriksa Redis pada sebuah server.
 *
 * Sebagian besar keterangan yang berguna hanya keluar dari `INFO`, yang
 * memerlukan autentikasi bila `requirepass` dipasang. Karena itu kelas ini
 * membaca sendiri `requirepass` dari berkas konfigurasi bila password tidak
 * diberikan — pengelolaan server memang biasanya berjalan sebagai root, dan
 * tanpa itu halaman pemantauan menjadi kosong tanpa alasan yang jelas.
 */
class CServer_Redis {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var null|string
     */
    protected $password;

    /**
     * @var null|string
     */
    protected $configFile;

    /**
     * @param CServer_Server $server
     * @param null|string    $password
     */
    public function __construct(CServer_Server $server, $password = null) {
        $this->server = $server;
        $this->password = $password;
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
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
     * Versi redis-server, atau null bila tidak terpasang.
     *
     * @return null|string
     */
    public function getVersion() {
        $output = trim($this->run('command -v redis-server >/dev/null 2>&1 && redis-server --version 2>&1 || echo REDIS_NOT_FOUND'));
        if (strlen($output) == 0 || strpos($output, 'REDIS_NOT_FOUND') !== false) {
            return null;
        }
        if (preg_match('/v=([0-9][0-9a-zA-Z.\-]*)/', $output, $m)) {
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
     * Berkas konfigurasi yang dipakai, dicari pada lokasi yang lazim.
     *
     * @return null|string
     */
    public function getConfigFile() {
        if ($this->configFile !== null) {
            return $this->configFile ?: null;
        }
        $output = trim($this->run(
            'for f in /etc/redis/redis.conf /etc/redis.conf /usr/local/etc/redis.conf /etc/redis/6379.conf; do'
            . ' [ -f "$f" ] && echo "$f" && break; done'
        ));
        $this->configFile = $output;

        return $output ?: null;
    }

    /**
     * Password dari `requirepass` di berkas konfigurasi.
     *
     * Mengembalikan null bila tidak dipasang atau berkasnya tidak terbaca —
     * keduanya tidak dibedakan, karena bagi pemanggil akibatnya sama.
     *
     * @return null|string
     */
    public function getPasswordFromConfig() {
        $file = $this->getConfigFile();
        if ($file === null) {
            return null;
        }
        $output = trim($this->run(
            'grep -m1 -E "^[[:space:]]*requirepass[[:space:]]+" ' . escapeshellarg($file) . ' 2>/dev/null | awk \'{print $2}\''
        ));

        return strlen($output) > 0 ? $output : null;
    }

    /**
     * @return string
     */
    protected function cli() {
        $password = $this->password;
        if ($password === null) {
            $password = $this->getPasswordFromConfig();
            $this->password = $password;
        }
        if ($password === null || strlen($password) == 0) {
            return 'redis-cli';
        }

        return 'redis-cli -a ' . escapeshellarg($password) . ' --no-auth-warning';
    }

    /**
     * Keluaran `INFO` yang sudah diurai menjadi pasangan kunci-nilai.
     *
     * @param null|string $section
     *
     * @return array kosong bila tidak dapat diakses
     */
    public function getInfo($section = null) {
        $command = $this->cli() . ' INFO' . ($section ? ' ' . $section : '') . ' 2>&1';
        $output = $this->run($command);

        if (stripos((string) $output, 'NOAUTH') !== false || stripos((string) $output, 'WRONGPASS') !== false) {
            return [];
        }

        $info = [];
        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (strlen($line) == 0 || substr($line, 0, 1) == '#' || strpos($line, ':') === false) {
                continue;
            }
            list($key, $value) = explode(':', $line, 2);
            $info[trim($key)] = trim($value);
        }

        return $info;
    }

    /**
     * @param string $pattern
     *
     * @return array
     */
    public function getConfig($pattern = '*') {
        $output = $this->run($this->cli() . ' CONFIG GET ' . escapeshellarg($pattern) . ' 2>&1');
        if (stripos((string) $output, 'NOAUTH') !== false || stripos((string) $output, 'WRONGPASS') !== false) {
            return [];
        }

        //keluarannya berselang-seling: nama, nilai, nama, nilai
        $line = array_values(array_filter(array_map('trim', explode("\n", (string) $output)), function ($v) {
            return strlen($v) > 0;
        }));
        $config = [];
        for ($i = 0; $i + 1 < count($line); $i += 2) {
            $config[$line[$i]] = $line[$i + 1];
        }

        return $config;
    }

    /**
     * Basis data beserta jumlah kuncinya, dari bagian keyspace.
     *
     * @return array
     */
    public function getKeyspace() {
        $info = $this->getInfo('keyspace');
        $keyspace = [];
        foreach ($info as $db => $value) {
            if (strpos($db, 'db') !== 0) {
                continue;
            }
            $detail = [];
            foreach (explode(',', $value) as $pair) {
                if (strpos($pair, '=') === false) {
                    continue;
                }
                list($k, $v) = explode('=', $pair, 2);
                $detail[trim($k)] = trim($v);
            }
            $keyspace[] = [
                'db' => $db,
                'keys' => (int) carr::get($detail, 'keys'),
                'expires' => (int) carr::get($detail, 'expires'),
                'avg_ttl' => (int) carr::get($detail, 'avg_ttl'),
            ];
        }

        return $keyspace;
    }

    /**
     * Status layanan systemd; nama unitnya berbeda antar distribusi.
     *
     * @return array unit => status
     */
    public function getServiceStatus() {
        $output = $this->run('for u in redis redis-server; do'
            . ' s=$(systemctl is-active $u 2>/dev/null); [ -n "$s" ] && echo "$u|$s"; done');
        $status = [];
        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (strpos($line, '|') === false) {
                continue;
            }
            list($unit, $state) = explode('|', $line, 2);
            $status[trim($unit)] = trim($state);
        }

        return $status;
    }

    /**
     * Hal-hal yang mudah terlewat karena hanya terlihat dari INFO atau
     * konfigurasi, padahal menentukan apakah Redis dapat menjatuhkan server.
     *
     * @return array daftar pesan
     */
    public function getWarningList() {
        $warning = [];

        $memory = $this->getInfo('memory');
        if (count($memory) == 0) {
            return ['Tidak dapat membaca INFO — Redis memerlukan autentikasi dan password tidak ditemukan'
                . ' pada berkas konfigurasi.'];
        }

        $maxmemory = (int) carr::get($memory, 'maxmemory');
        $policy = carr::get($memory, 'maxmemory_policy');
        if ($maxmemory === 0) {
            $warning[] = 'maxmemory tidak dibatasi, sehingga Redis akan terus tumbuh sampai memori server habis.';
        }
        if ($maxmemory === 0 && $policy == 'noeviction') {
            $warning[] = 'Kebijakan maxmemory adalah noeviction — tidak ada kunci yang akan diusir,'
                . ' jadi penulisan akan ditolak begitu memori penuh alih-alih memberi ruang.';
        }

        $persistence = $this->getInfo('persistence');
        if (count($persistence) > 0) {
            $aof = carr::get($persistence, 'aof_enabled');
            $lastSave = (int) carr::get($persistence, 'rdb_last_save_time');
            $changes = (int) carr::get($persistence, 'rdb_changes_since_last_save');
            if ($aof === '0' && $lastSave === 0) {
                $warning[] = 'Tidak ada persistensi aktif (AOF mati dan RDB belum pernah tersimpan),'
                    . ' sehingga seluruh isi hilang bila Redis berhenti.';
            } elseif ($changes > 10000) {
                $warning[] = 'Ada ' . number_format($changes) . ' perubahan sejak penyimpanan RDB terakhir.';
            }
        }

        $config = $this->getConfig('bind');
        $bind = carr::get($config, 'bind');
        if ($bind !== null && (strpos($bind, '0.0.0.0') !== false || trim($bind) == '')) {
            $password = $this->password ?: $this->getPasswordFromConfig();
            if ($password === null) {
                $warning[] = 'Redis mendengarkan di seluruh antarmuka tanpa requirepass.';
            }
        }

        return $warning;
    }
}
