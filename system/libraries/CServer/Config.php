<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Config {
    /**
     * @var array
     */
    protected static $instance = [];

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $configBefore;

    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @param CServer_Server $server
     */
    public function __construct(?CServer_Server $server = null) {
        if ($server === null) {
            $server = CServer::server();
        }
        $this->server = $server;

        $defaultConfig = [
            'use_vhost' => false,
            'debug' => false,
            'load_percent_enabled' => true,
            'os' => null,
            'system_codepage' => null,
            'system_lang' => null,
            'unameo' => null,
            'mode_popen' => null,
            'log' => false,
            'rootfs' => false,
            'sudo_commands' => false,
            'add_paths' => false,
            'hide_fs_types' => false,
        ];

        if ($this->server->isLocal()) {
            $this->config = array_merge($defaultConfig, CF::config('server', []));
        } else {
            $this->config = $defaultConfig;
        }

        $this->init();
        $this->configBefore = $this->config;
    }

    /**
     * @param CServer_Server $server
     *
     * @return CServer_Config
     */
    public static function instance(?CServer_Server $server = null) {
        if ($server === null) {
            $server = CServer::server();
        }

        $host = $server->isRemote() ? $server->getSSH()->getHost() : 'localhost';
        if (!isset(self::$instance[$host])) {
            self::$instance[$host] = new CServer_Config($server);
        }

        return self::$instance[$host];
    }

    /**
     * @return void
     */
    public function init() {
        if ($this->server->isRemote()) {
            $this->initRemote();
        } else {
            $this->initLocal();
        }
    }

    /**
     * @return void
     */
    protected function initRemote() {
        $os = trim($this->server->runCommand('uname -s'));
        if (strlen($os) > 0) {
            $this->set('os', $os);
        }

        $codepage = trim($this->server->runCommand('locale charmap 2>/dev/null'));
        if (strlen($codepage) > 0) {
            $this->set('system_codepage', $codepage);
        }

        $lang = trim($this->server->runCommand('echo $LANG'));
        if (strlen($lang) > 0 && $this->get('system_lang') === null) {
            $langCode = preg_replace('/[\.@].*$/', '', $lang);
            $langdata = CServer_Const::$languages;
            $langName = '';
            if (isset($langdata['Linux']['_' . $langCode])) {
                $langName = $langdata['Linux']['_' . $langCode];
            }
            if ($langName == '') {
                $langName = 'Unknown';
            }
            $this->set('system_lang', $langName . ' (' . $langCode . ')');
        }
    }

    /**
     * @return void
     */
    protected function initLocal() {
        if (!function_exists('proc_open')) {
            $this->set('mode_popen', true);
        }
        if ($this->get('os') === null) {
            if (PHP_OS == 'Linux') {
                if (@file_exists($fname = '/etc/sysconfig/i18n') || @file_exists($fname = '/etc/default/locale') || @file_exists($fname = '/etc/locale.conf') || @file_exists($fname = '/etc/sysconfig/language') || @file_exists($fname = '/etc/profile.d/lang.sh') || @file_exists($fname = '/etc/profile')) {
                    $contents = @file_get_contents($fname);
                } else {
                    $contents = false;
                    if (@file_exists('/system/build.prop')) {
                        $this->set('os', 'Android');
                        if (@exec('uname -o 2>/dev/null', $unameo) && (sizeof($unameo) > 0) && (($unameo0 = trim($unameo[0])) != '')) {
                            $this->set('unameo', $unameo0);
                        }
                        if ($this->get('mode_popen') === null) {
                            if (!function_exists('proc_open')) {
                                $this->set('mode_popen', true);
                            } else {
                                $out = '';
                                $err = '';
                                $pipes = [];
                                $descriptorspec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
                                $process = proc_open('pwd 2>/dev/null ', $descriptorspec, $pipes);
                                if (!is_resource($process)) {
                                    $this->set('mode_popen', true);
                                } else {
                                    $w = null;
                                    $e = null;

                                    while (!(feof($pipes[1]) && feof($pipes[2]))) {
                                        $read = [$pipes[1], $pipes[2]];

                                        $n = stream_select($read, $w, $e, 5);

                                        if (($n === false) || ($n === 0)) {
                                            break;
                                        }

                                        foreach ($read as $r) {
                                            if ($r == $pipes[1]) {
                                                $out .= fread($r, 4096);
                                            } elseif (feof($pipes[1]) && ($r == $pipes[2])) {
                                                $err .= fread($r, 4096);
                                            }
                                        }
                                    }

                                    if (is_null($out) || (trim($out) == '') || (substr(trim($out), 0, 1) != '/')) {
                                        $this->set('mode_popen', true);
                                    }
                                    fclose($pipes[0]);
                                    fclose($pipes[1]);
                                    fclose($pipes[2]);
                                    proc_close($process);
                                }
                            }
                        }
                    }
                }
                if (!($this->get('system_codepage') !== null
                    && $this->get('system_lang') !== null)
                    && $contents && (preg_match('/^(LANG="?[^"\n]*"?)/m', $contents, $matches) || preg_match('/^RC_(LANG="?[^"\n]*"?)/m', $contents, $matches) || preg_match('/^\s*export (LANG="?[^"\n]*"?)/m', $contents, $matches))
                ) {
                    if ($this->get('system_codepage') === null) {
                        if (file_exists($vtfname = '/sys/module/vt/parameters/default_utf8') && (trim(@file_get_contents($vtfname)) === '1')) {
                            $this->set('system_codepage', 'UTF-8');
                        } elseif (@exec($matches[1] . ' locale -k LC_CTYPE 2>/dev/null', $lines)) {
                            foreach ($lines as $line) {
                                if (preg_match('/^charmap="?([^"]*)/', $line, $matches2)) {
                                    $this->set('system_codepage', $matches2[1]);

                                    break;
                                }
                            }
                        }
                    }
                    if ($this->get('system_lang') === null && @exec($matches[1] . ' locale 2>/dev/null', $lines2)) {
                        foreach ($lines2 as $line) {
                            if (preg_match('/^LC_MESSAGES="?([^\."@]*)/', $line, $matches2)) {
                                $lang = '';
                                $langdata = CServer_Const::$languages;
                                if (isset($langdata['Linux']['_' . $matches2[1]])) {
                                    $lang = $langdata['Linux']['_' . $matches2[1]];
                                }
                                if ($lang == '') {
                                    $lang = 'Unknown';
                                }
                                $this->set('system_lang', $lang . ' (' . $matches2[1] . ')');

                                break;
                            }
                        }
                    }
                }
            }
        }

        if ($this->get('os') === null) {
            $this->set('os', PHP_OS);
        }

        if ($this->get('system_codepage') === null) {
            if (($this->get('os') == 'Android') || ($this->get('os') == 'Darwin')) {
                $this->set('system_codepage', 'UTF-8');
            }
            if ($this->get('os') == 'Minix') {
                $this->set('system_codepage', 'CP437');
            }
        }
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key) {
        return carr::get($this->config, $key);
    }

    /**
     * @return array
     */
    public function getAll() {
        return $this->config;
    }

    /**
     * @param string $key
     * @param mixed  $val
     *
     * @return $this
     */
    public function set($key, $val) {
        $this->config[$key] = $val;

        return $this;
    }

    /**
     * @return void
     */
    public function reset() {
        $this->config = $this->configBefore;
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @return bool
     */
    public function isUseVHost() {
        return $this->get('use_vhost') === true;
    }

    /**
     * @return bool
     */
    public function isDebug() {
        return $this->get('debug') === true;
    }

    /**
     * @return bool
     */
    public function isLoadPercentEnabled() {
        return $this->get('load_percent_enabled') === true;
    }

    /**
     * @return bool
     */
    public function isModePopen() {
        return $this->get('mode_popen') === true;
    }

    /**
     * @return null|string
     */
    public function getLog() {
        return $this->get('log');
    }

    /**
     * @return null|bool|string
     */
    public function getRootFs() {
        return $this->get('rootfs');
    }

    /**
     * @return bool|string
     */
    public function getUnameo() {
        return $this->get('unameo');
    }

    /**
     * @return bool|string
     */
    public function getSudoCommands() {
        return $this->get('sudo_commands');
    }

    /**
     * @return bool|string
     */
    public function getAddPaths() {
        return $this->get('add_paths');
    }

    /**
     * @return bool|string
     */
    public function getHideFsTypes() {
        return $this->get('hide_fs_types');
    }
}
