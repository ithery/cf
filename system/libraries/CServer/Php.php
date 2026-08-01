<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Php {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $phpBinary;

    /**
     * @param CServer_Server $server
     * @param null|string    $phpBinary
     */
    public function __construct(CServer_Server $server, $phpBinary = null) {
        $this->server = $server;
        $this->phpBinary = $phpBinary ?: 'php';
    }

    /**
     * @return string
     */
    public function getPhpBinary() {
        return $this->phpBinary;
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getVersion() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand($this->phpBinary . ' -r "echo phpversion();"'));
        }

        return phpversion();
    }

    /**
     * @param string $extName
     *
     * @return string
     */
    public function getExtVersion($extName) {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand($this->phpBinary . ' -r "echo phpversion(\'' . $extName . '\');"'));
        }

        return phpversion($extName);
    }

    /**
     * @return array
     */
    public function getAllIniConfiguration() {
        if ($this->server->isRemote()) {
            $output = trim($this->server->runCommand($this->phpBinary . ' -r "echo json_encode(ini_get_all());"'));

            return json_decode($output, true) ?: [];
        }

        return ini_get_all();
    }

    /**
     * @param string $extName
     *
     * @return array
     */
    public function getAllIniConfigurationExt($extName) {
        if ($this->server->isRemote()) {
            $output = trim($this->server->runCommand($this->phpBinary . ' -r "echo json_encode(ini_get_all(\'' . $extName . '\'));"'));

            return json_decode($output, true) ?: [];
        }

        return ini_get_all($extName);
    }

    /**
     * @param string $varName
     *
     * @return string
     */
    public function getIniConfiguration($varName) {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand($this->phpBinary . ' -r "echo ini_get(\'' . $varName . '\');"'));
        }

        return ini_get($varName);
    }

    /**
     * @return string
     */
    public function getIniLoadedFile() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand($this->phpBinary . ' -r "echo php_ini_loaded_file();"'));
        }

        return php_ini_loaded_file();
    }

    /**
     * @return string
     */
    public function getSapiName() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand($this->phpBinary . ' -r "echo php_sapi_name();"'));
        }

        return php_sapi_name();
    }

    /**
     * @return string
     */
    public function getTempDir() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand($this->phpBinary . ' -r "echo sys_get_temp_dir();"'));
        }

        return sys_get_temp_dir();
    }

    /**
     * @return string
     */
    public function getCurrentUser() {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand('whoami'));
        }

        return get_current_user();
    }

    /**
     * Seluruh PHP yang terpasang di server ini beserta pengaturan pentingnya.
     *
     * Satu server sering punya lebih dari satu PHP: binari dari pengelola
     * paket, lsphp milik LiteSpeed, dan php-fpm per versi. Semuanya diperiksa
     * dalam satu perintah karena tiap perjalanan SSH berbiaya mahal, dan jalur
     * symlink diselesaikan agar `php` dan `php8.3` tidak terhitung dua kali.
     *
     * @param CServer_Server $server
     * @param array          $iniList pengaturan yang ingin dibaca
     *
     * @return array default, versions, fpm
     */
    public static function inspectAll(CServer_Server $server, array $iniList = null) {
        if ($iniList === null) {
            $iniList = [
                'memory_limit', 'max_execution_time', 'upload_max_filesize',
                'post_max_size', 'date.timezone', 'opcache.enable',
            ];
        }
        $iniArg = implode(' ', $iniList);

        $script = 'BIN=""; '
            . 'for c in php php5.6 php7.0 php7.1 php7.2 php7.3 php7.4 php8.0 php8.1 php8.2 php8.3 php8.4 php8.5; do '
            . '  p=$(command -v $c 2>/dev/null); [ -n "$p" ] && BIN="$BIN $p"; '
            . 'done; '
            . 'for p in /usr/local/lsws/lsphp*/bin/php; do [ -x "$p" ] && BIN="$BIN $p"; done; '
            . 'BIN=$(for p in $BIN; do readlink -f "$p"; done | sort -u); '
            . 'DEF=$(readlink -f "$(command -v php 2>/dev/null)" 2>/dev/null); '
            . 'echo "DEFAULT|$DEF"; '
            . 'for p in $BIN; do '
            . '  v=$("$p" -r "echo PHP_VERSION;" 2>/dev/null); [ -z "$v" ] && continue; '
            . '  ini=$("$p" -r "echo php_ini_loaded_file() ?: \'-\';" 2>/dev/null); '
            . '  s=""; for k in ' . $iniArg . '; do '
            . '    val=$("$p" -r "\\$x=ini_get(\'$k\'); echo (\\$x===false||\\$x===\'\')?\'-\':\\$x;" 2>/dev/null); '
            . '    s="$s$k=$val;"; done; '
            . '  echo "BIN|$p|$v|$ini|$s"; '
            . '  echo "EXT|$p|$("$p" -r "echo implode(\',\', get_loaded_extensions());" 2>/dev/null)"; '
            . 'done; '
            . 'echo "FPM|$(systemctl list-units --type=service --all 2>/dev/null | grep -oE \'php[0-9.]*-fpm\' | sort -u | tr \'\\n\' \',\')"';

        $output = $server->runCommand($script);

        $result = ['default' => null, 'versions' => [], 'fpm' => []];
        $extension = [];

        foreach (explode("\n", (string) $output) as $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            $part = explode('|', $line);
            $type = carr::get($part, 0);

            if ($type == 'DEFAULT') {
                $result['default'] = trim((string) carr::get($part, 1)) ?: null;
            } elseif ($type == 'BIN') {
                $path = carr::get($part, 1);
                $setting = [];
                foreach (explode(';', (string) carr::get($part, 4)) as $pair) {
                    if (strpos($pair, '=') === false) {
                        continue;
                    }
                    list($k, $v) = explode('=', $pair, 2);
                    $setting[$k] = $v;
                }
                $result['versions'][$path] = [
                    'path' => $path,
                    'version' => carr::get($part, 2),
                    'ini' => carr::get($part, 3),
                    'setting' => $setting,
                    'extension' => [],
                    'litespeed' => strpos($path, '/lsws/') !== false,
                ];
            } elseif ($type == 'EXT') {
                $path = carr::get($part, 1);
                $extension[$path] = array_values(array_filter(array_map('trim', explode(',', (string) carr::get($part, 2)))));
            } elseif ($type == 'FPM') {
                $result['fpm'] = array_values(array_filter(array_map('trim', explode(',', (string) carr::get($part, 1)))));
            }
        }

        foreach ($extension as $path => $list) {
            if (isset($result['versions'][$path])) {
                $result['versions'][$path]['extension'] = $list;
            }
        }
        $result['versions'] = array_values($result['versions']);

        return $result;
    }

    /**
     * Apakah sebuah versi PHP sudah tidak lagi menerima perbaikan keamanan.
     *
     * Ditentukan dari nomor mayor-minor, bukan tanggal, agar tidak memerlukan
     * tabel yang harus terus diperbarui.
     *
     * @param string $version
     *
     * @return bool
     */
    public static function isEndOfLife($version) {
        if (!preg_match('/^(\d+)\.(\d+)/', (string) $version, $m)) {
            return false;
        }
        $major = (int) $m[1];
        $minor = (int) $m[2];
        if ($major < 8) {
            return true;
        }

        return $major == 8 && $minor <= 1;
    }
}
