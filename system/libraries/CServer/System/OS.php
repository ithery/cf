<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CServer_System_OS implements CServer_System_OSInterface {
    /**
     * @var CServer_System_Info
     */
    protected $info;

    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @param CServer_Server      $server
     * @param CServer_System_Info $info
     */
    public function __construct(CServer_Server $server, CServer_System_Info $info) {
        $this->server = $server;
        $this->info = $info;
    }

    /**
     * @return void
     */
    public function buildIp() {
        $result = '';
        $config = $this->server->config();
        if ($config->isUseVHost()) {
            if (($this->server->readEnv('SERVER_ADDR', $result) || $this->server->readEnv('LOCAL_ADDR', $result))
                && !strstr($result, '.') && strstr($result, ':')
            ) {
                $dnsrec = dns_get_record($this->info->getHostname(), DNS_AAAA);
                if (isset($dnsrec[0]['ipv6'])) {
                    $this->info->setIp($dnsrec[0]['ipv6']);
                } else {
                    $this->info->setIp(preg_replace('/^::ffff:/i', '', $result));
                }
            } else {
                $this->info->setIp(gethostbyname($this->info->getHostname()));
            }
        } else {
            if ($this->server->readEnv('SERVER_ADDR', $result) || $this->server->readEnv('LOCAL_ADDR', $result)) {
                $this->info->setIp(preg_replace('/^::ffff:/i', '', $result));
            } else {
                $this->info->setIp(gethostbyname($this->info->getHostname()));
            }
        }
    }

    /**
     * @return void
     */
    public function buildUsers() {
        $buf = '';
        $config = $this->server->config();
        if ($this->server->executeProgram('who', '', $buf, $config->isDebug())) {
            if (strlen($buf) > 0) {
                $lines = preg_split('/\n/', $buf);
                $this->info->setUsers(count($lines));
            }
        } elseif ($this->server->executeProgram('uptime', '', $buf, $config->isDebug()) && preg_match("/,\s+(\d+)\s+user[s]?,/", $buf, $m)) {
            $this->info->setUsers($m[1]);
        }
    }
}
