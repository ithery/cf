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
            $this->info = $this->parsePhpInfoHtml($this->getPhpInfoHtml());
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
        $data = $this->parsePhpInfoHtml($this->getPhpInfoHtml($filter));

        return c::collect(array_map(function ($section) {
            if (is_array($section)) {
                return c::collect(array_map(function ($val) {
                    return is_array($val) ? c::collect($val) : $val;
                }, $section));
            }

            return $section;
        }, $data));
    }

    /**
     * @param int $filter
     *
     * @return string
     */
    protected function getPhpInfoHtml($filter = INFO_ALL) {
        if ($this->server->isRemote()) {
            return trim($this->server->runCommand($this->server->php()->getPhpBinary() . ' -r "phpinfo(' . $filter . ');"'));
        }

        ob_start();
        @phpinfo($filter);

        return ob_get_clean();
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
