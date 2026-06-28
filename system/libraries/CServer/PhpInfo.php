<?php

defined('SYSPATH') or die('No direct access allowed.');

use CServer_PhpInfo_Filter as Filter;

final class CServer_PhpInfo {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var array
     */
    protected $info = [];

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
     * @return array
     */
    public function get() {
        if (empty($this->info)) {
            if ($this->server->isRemote()) {
                $this->info = $this->getRemotePhpInfo();
            } else {
                $this->info = $this->getLocalPhpInfo();
            }
        }

        return $this->info;
    }

    /**
     * @return string
     */
    public function getPhpVersion() {
        return $this->server->php()->getVersion();
    }

    /**
     * @param int $filter
     *
     * @return \CCollection
     */
    public function toCollection($filter = Filter::ALL) {
        if ($this->server->isRemote()) {
            $html = trim($this->server->runCommand($this->server->php()->getPhpBinary() . ' -r "phpinfo(' . $filter . ');"'));
        } else {
            ob_start();
            phpinfo($filter);
            $html = ob_get_clean();
        }

        $phpinfo = ['phpinfo' => c::collect()];

        if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', $html, $matches, PREG_SET_ORDER)) {
            c::collect($matches)->each(function ($match) use (&$phpinfo) {
                if (strlen($match[1])) {
                    $phpinfo[$match[1]] = c::collect();
                } elseif (isset($match[3])) {
                    $keys1 = array_keys($phpinfo);
                    $phpinfo[end($keys1)][$match[2]] = isset($match[4]) ? c::collect([$match[3], $match[4]]) : $match[3];
                } else {
                    $keys1 = array_keys($phpinfo);
                    $phpinfo[end($keys1)][] = $match[2];
                }
            });
        }

        return c::collect($phpinfo);
    }

    /**
     * @return array
     */
    protected function getLocalPhpInfo() {
        ob_start();
        @phpinfo();
        $html = ob_get_clean();

        return $this->parsePhpInfoHtml($html);
    }

    /**
     * @return array
     */
    protected function getRemotePhpInfo() {
        $html = trim($this->server->runCommand($this->server->php()->getPhpBinary() . ' -r "phpinfo();"'));

        return $this->parsePhpInfoHtml($html);
    }

    /**
     * @param string $html
     *
     * @return array
     */
    protected function parsePhpInfoHtml($html) {
        $phpinfo = ['phpinfo' => []];
        $matches = [];
        if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (strlen($match[1])) {
                    $phpinfo[$match[1]] = [];
                } elseif (isset($match[3])) {
                    $keys = array_keys($phpinfo);
                    $phpinfo[end($keys)][$match[2]] = isset($match[4]) ? [$match[3], $match[4]] : $match[3];
                } else {
                    $keys = array_keys($phpinfo);
                    $phpinfo[end($keys)][] = $match[2];
                }
            }
        }

        return $phpinfo;
    }
}
