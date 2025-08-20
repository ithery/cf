<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 1:48:09 PM
 */
abstract class CServer_System_OS implements CServer_System_OSInterface {
    /**
     * @var CServer_System_Info
     */
    protected $info;

    protected $system;

    /**
     * @param CServer_System_Info $info
     */
    public function __construct(CServer_System $system, CServer_System_Info $info) {
        $this->info = $info;
        $this->system = $system;
    }

    /**
     * IP of the Host.
     *
     * @return void
     */
    public function buildIp() {
        $cmd = $this->createCommand();
        if (CServer::config()->isUseVHost()) {
            if (($cmd->readEnv('SERVER_ADDR', $result) || $cmd->readEnv('LOCAL_ADDR', $result)) //is server address defined
                && !strstr($result, '.') && strstr($result, ':')
            ) { //is IPv6, quick version of preg_match('/\(([[0-9A-Fa-f\:]+)\)/', $result)
                $dnsrec = dns_get_record($this->info->getHostname(), DNS_AAAA);
                if (isset($dnsrec[0]['ipv6'])) { //is DNS IPv6 record
                    $this->info->setIp($dnsrec[0]['ipv6']); //from DNS (avoid IPv6 NAT translation)
                } else {
                    $this->info->setIp(preg_replace('/^::ffff:/i', '', $result)); //from SERVER_ADDR or LOCAL_ADDR
                }
            } else {
                $this->info->setIp(gethostbyname($this->info->getHostname())); //IPv4 only
            }
        } else {
            if ($cmd->readEnv('SERVER_ADDR', $result) || $cmd->readEnv('LOCAL_ADDR', $result)) {
                $this->info->setIp(preg_replace('/^::ffff:/i', '', $result));
            } else {
                $this->info->setIp(gethostbyname($this->info->getHostname()));
            }
        }
    }

    /**
     * Number of Users.
     *
     * @return void
     */
    public function buildUsers() {
        $cmd = $this->createCommand();
        if ($cmd->executeProgram('who', '', $strBuf, CServer::config()->isDebug())) {
            if (strlen($strBuf) > 0) {
                $lines = preg_split('/\n/', $strBuf);
                $this->info->setUsers(count($lines));
            }
        } elseif ($cmd->executeProgram('uptime', '', $buf, CServer::config()->isDebug()) && preg_match("/,\s+(\d+)\s+user[s]?,/", $buf, $ar_buf)) {
            //} elseif ($cmd->executeProgram('uptime', '', $buf) && preg_match("/,\s+(\d+)\s+user[s]?,\s+load average[s]?:\s+(.*),\s+(.*),\s+(.*)$/", $buf, $ar_buf)) {
            $this->info->setUsers($ar_buf[1]);
        } else {
            $processlist = glob('/proc/*/cmdline', GLOB_NOSORT);
            if (is_array($processlist) && (($total = count($processlist)) > 0)) {
                $count = 0;
                $buf = '';
                for ($i = 0; $i < $total; $i++) {
                    if ($cmd->rfts($processlist[$i], $buf, 0, 4096, false)) {
                        $name = str_replace(chr(0), ' ', trim($buf));
                        if (preg_match('/^-/', $name)) {
                            $count++;
                        }
                    }
                }
                if ($count > 0) {
                    $this->info->setUsers($count);
                }
            }
        }
    }

    public function createCommand() {
        return CServer::command($this->system->getSSH());
    }
}
