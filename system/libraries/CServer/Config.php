<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 3:17:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Config {

    protected static $instance;
    protected $config;
    protected $configBefore;

    public function init() {
        if (!function_exists("proc_open")) { //proc_open function test by executing 'pwd' command
            $this->set('mode_popen', true);
        }
        if ($this->get('os') === null) { //if not overloaded in config server.php
            /* get Linux code page */
            if (PHP_OS == 'Linux') {
                if (file_exists($fname = '/etc/sysconfig/i18n') || file_exists($fname = '/etc/default/locale') || file_exists($fname = '/etc/locale.conf') || file_exists($fname = '/etc/sysconfig/language') || file_exists($fname = '/etc/profile.d/lang.sh') || file_exists($fname = '/etc/profile')) {
                    $contents = @file_get_contents($fname);
                } else {
                    $contents = false;
                    if (file_exists('/system/build.prop')) { //Android
                        $this->set('os', 'Android');
                        if (@exec('uname -o 2>/dev/null', $unameo) && (sizeof($unameo) > 0) && (($unameo0 = trim($unameo[0])) != "")) {
                            $this->set('unameo', $unameo0);
                        }
                        if ($this->get('mode_popen') === null) {//if not overloaded in config server.php
                            if (!function_exists("proc_open")) { //proc_open function test by executing 'pwd' command
                                $this->set('mode_popen', true); //use popen() function - no stderr error handling (but with problems with timeout)
                            } else {
                                $out = '';
                                $err = '';
                                $pipes = array();
                                $descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w"));
                                $process = proc_open("pwd 2>/dev/null ", $descriptorspec, $pipes);
                                if (!is_resource($process)) {
                                    $this->set('mode_popen', true);
                                } else {
                                    $w = null;
                                    $e = null;

                                    while (!(feof($pipes[1]) && feof($pipes[2]))) {
                                        $read = array($pipes[1], $pipes[2]);

                                        $n = stream_select($read, $w, $e, 5);

                                        if (($n === false) || ($n === 0)) {
                                            break;
                                        }

                                        foreach ($read as $r) {
                                            if ($r == $pipes[1]) {
                                                $out .= fread($r, 4096);
                                            } elseif (feof($pipes[1]) && ($r == $pipes[2])) {//read STDERR after STDOUT
                                                $err .= fread($r, 4096);
                                            }
                                        }
                                    }

                                    if (is_null($out) || (trim($out) == "") || (substr(trim($out), 0, 1) != "/")) {
                                        $this->set('mode_popen', true);
                                    }
                                    fclose($pipes[0]);
                                    fclose($pipes[1]);
                                    fclose($pipes[2]);
                                    // It is important that you close any pipes before calling
                                    // proc_close in order to avoid a deadlock
                                    proc_close($process);
                                }
                            }
                        }
                    }
                }
                if (!($this->get('system_codepage') !== null && $this->get('system_lang') !== null) //also if both not overloaded in config server.php
                        && $contents && (preg_match('/^(LANG="?[^"\n]*"?)/m', $contents, $matches) || preg_match('/^RC_(LANG="?[^"\n]*"?)/m', $contents, $matches) || preg_match('/^\s*export (LANG="?[^"\n]*"?)/m', $contents, $matches))) {
                    if ($this->get('system_codepage') === null) {
                        if (file_exists($vtfname = '/sys/module/vt/parameters/default_utf8') && (trim(@file_get_contents($vtfname)) === "1")) {
                            $this->set('system_codepage', 'UTF-8');
                        } elseif (@exec($matches[1] . ' locale -k LC_CTYPE 2>/dev/null', $lines)) { //if not overloaded in config server.php
                            foreach ($lines as $line) {
                                if (preg_match('/^charmap="?([^"]*)/', $line, $matches2)) {
                                    $this->set('system_codepage', $matches2[1]);
                                    break;
                                }
                            }
                        }
                    }
                    if ($this->get('system_lang') === null && @exec($matches[1] . ' locale 2>/dev/null', $lines2)) { //also if not overloaded in config server.php
                        foreach ($lines2 as $line) {
                            if (preg_match('/^LC_MESSAGES="?([^\."@]*)/', $line, $matches2)) {
                                $lang = "";
                                $langdata = CServer_Const::$languages;
                                if (isset($langdata['Linux']['_' . $matches2[1]])) {
                                    $lang = $langdata['Linux']['_' . $matches2[1]];
                                }
                                if ($lang == "") {
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


        if ($this->get('system_codepage') === null) { //if not overloaded in config server.php
            if (($this->get('os') == 'Android') || ($this->get('os') == 'Darwin')) {
                $this->set('system_codepage', 'UTF-8');
            }
            if ($this->get('os') == 'Minix') {
                $this->set('system_codepage', 'CP437');
            }
        }
    }

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CServer_Config();
        }
        return self::$instance;
    }

    public function __construct() {

        $defaultConfig = array(
            'use_vhost' => false,
            'debug' => false,
            'load_percent_enabled' => true,
            'os' => null,
            'system_codepage' => null,
            'system_lang' => null,
            'unameo' => null,
            'mode_popen' => null,
        );

        $this->config = array_merge($defaultConfig, CF::config('server', array()));
        $this->init();

        $this->configBefore = $this->config;
    }

    public function get($key) {
        return carr::get($this->config, $key);
    }

    public function getAll() {
        return $this->config;
    }

    public function set($key, $val) {
        $this->config[$key] = $val;
        return $this;
    }

    public function reset() {
        $this->config = $this->configBefore;
    }

    public function isUseVHost() {
        return $this->get('use_vhost') === true;
    }

    public function isDebug() {
        return $this->get('debug') === true;
    }

    public function loadPercentEnabled() {
        return $this->get('load_percent_enabled') === true;
    }

}
