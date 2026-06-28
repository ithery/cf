<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_System {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var CServer_System_Info
     */
    protected $info;

    /**
     * @var null|array
     */
    protected $cpuLoads;

    /**
     * @param CServer_Server $server
     */
    public function __construct(CServer_Server $server) {
        $this->server = $server;
        $this->info = new CServer_System_Info();
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
    public function getHostname() {
        if (!$this->info->getHostname()) {
            $this->buildHostname();
        }

        return $this->info->getHostname();
    }

    /**
     * @return string
     */
    public function getIp() {
        if (!$this->info->getIp()) {
            $this->buildIp();
        }

        return $this->info->getIp();
    }

    /**
     * @return float
     */
    public function getUptime() {
        if (!$this->info->getUptime()) {
            $this->buildUptime();
        }

        return $this->info->getUptime();
    }

    /**
     * @return string
     */
    public function getKernel() {
        if (!$this->info->getKernel()) {
            $this->buildKernel();
        }

        return $this->info->getKernel();
    }

    /**
     * @return string
     */
    public function getDistribution() {
        if (!$this->info->getDistribution()) {
            $this->buildDistro();
        }

        return $this->info->getDistribution();
    }

    /**
     * @return string
     */
    public function getDistributionIcon() {
        if (!$this->info->getDistributionIcon()) {
            $this->buildDistro();
        }

        return $this->info->getDistributionIcon();
    }

    /**
     * @return int
     */
    public function getLastBoot() {
        $uptime = $this->getUptime();

        return time() - intval($uptime);
    }

    /**
     * @return int
     */
    public function getUsers() {
        if (!$this->info->getUsers()) {
            $this->buildUsers();
        }

        return $this->info->getUsers();
    }

    /**
     * @return array
     */
    public function getProcesses() {
        if (!$this->info->getProcesses()) {
            $this->buildProcesses();
        }

        return $this->info->getProcesses();
    }

    /**
     * @return string
     */
    public function getLoad() {
        if (!$this->info->getLoad()) {
            $this->buildLoadAvg();
        }

        return $this->info->getLoad();
    }

    /**
     * @return float
     */
    public function getLoadPercent() {
        if (!$this->info->getLoadPercent()) {
            $this->buildLoadAvg();
        }

        return $this->info->getLoadPercent();
    }

    /**
     * @return string
     */
    public function getMachine() {
        if (!$this->info->getMachine()) {
            $this->buildMachine();
        }

        return $this->info->getMachine();
    }

    /**
     * @return array
     */
    public function getCpus() {
        if (!$this->info->getCpus()) {
            $this->buildCpuInfo();
        }

        return $this->info->getCpus();
    }

    /**
     * @return void
     */
    protected function buildHostname() {
        $buf = '';
        $config = $this->server->config();
        if ($config->isUseVHost()) {
            if ($this->server->readEnv('SERVER_NAME', $buf)) {
                $this->info->setHostname($buf);
            }
        } else {
            if ($this->server->rfts('/proc/sys/kernel/hostname', $buf, 1)) {
                $result = trim($buf);
                if ($this->server->isLocal()) {
                    $ip = gethostbyname($result);
                    if ($ip != $result) {
                        $this->info->setHostname(gethostbyaddr($ip));
                    } else {
                        $this->info->setHostname($result);
                    }
                } else {
                    $this->info->setHostname($result);
                }
            } elseif ($this->server->executeProgram('hostname', '', $buf)) {
                $this->info->setHostname($buf);
            }
        }
    }

    /**
     * @return void
     */
    protected function buildIp() {
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
    protected function buildUptime() {
        $buf = '';
        if ($this->server->rfts('/proc/uptime', $buf, 1)) {
            $parts = preg_split('/ /', $buf);
            $this->info->setUptime(trim($parts[0]));
        } elseif ($this->server->executeProgram('uptime', '', $buf)) {
            if (preg_match("/up (\d+) day[s]?,[ ]+(\d+):(\d+),/", $buf, $m)) {
                $this->info->setUptime($m[1] * 86400 + $m[2] * 3600 + $m[3] * 60);
            } elseif (preg_match("/up (\d+) day[s]?,[ ]+(\d+) min,/", $buf, $m)) {
                $this->info->setUptime($m[1] * 86400 + $m[2] * 60);
            } elseif (preg_match("/up[ ]+(\d+):(\d+),/", $buf, $m)) {
                $this->info->setUptime($m[1] * 3600 + $m[2] * 60);
            } elseif (preg_match("/up[ ]+(\d+) min,/", $buf, $m)) {
                $this->info->setUptime($m[1] * 60);
            }
        }
    }

    /**
     * @return void
     */
    protected function buildKernel() {
        $result = '';
        $buf = '';
        $config = $this->server->config();
        if ($this->server->executeProgram('uname', '-r', $buf, $config->isDebug())) {
            $result = $buf;
            if ($this->server->executeProgram('uname', '-v', $buf, $config->isDebug())) {
                if (preg_match('/SMP/', $buf)) {
                    $result .= ' (SMP)';
                }
            }
            if ($this->server->executeProgram('uname', '-m', $buf, $config->isDebug())) {
                $result .= ' ' . $buf;
            }
        } elseif ($this->server->rfts('/proc/version', $buf, 1) && preg_match('/version\s+(\S+)/', $buf, $m)) {
            $result = $m[1];
            if (preg_match('/SMP/', $buf)) {
                $result .= ' (SMP)';
            }
        }
        if ($result != '') {
            $buf2 = '';
            if ($this->server->rfts('/proc/self/cgroup', $buf2, 0, 4096, false)) {
                if (preg_match('/:\/lxc\//m', $buf2)) {
                    $result .= ' [lxc]';
                } elseif (preg_match('/:\/docker\//m', $buf2) || preg_match('/:\/system\.slice\/docker\-/m', $buf2)) {
                    $result .= ' [docker]';
                }
            }
            if ($this->server->rfts('/proc/version', $buf2, 1, 4096, false) && preg_match('/^Linux version [\d\.-]+-Microsoft/', $buf2)) {
                $result .= ' [lxss]';
            }
            $this->info->setKernel($result);
        }
    }

    /**
     * @return void
     */
    protected function buildDistro() {
        $this->info->setDistribution('Linux');
        $list = CServer_Const::$distros;
        if (!$list) {
            return;
        }

        $buf = '';
        $config = $this->server->config();
        if ($this->server->executeProgram('lsb_release', '-a 2>/dev/null', $buf, $config->isDebug()) && (strlen($buf) > 0)) {
            $this->parseDistroFromLsbRelease($buf, $list);
        } else {
            $this->parseDistroFromFiles($list);
        }
    }

    /**
     * @param string $buf
     * @param array  $list
     *
     * @return void
     */
    protected function parseDistroFromLsbRelease($buf, $list) {
        $distro = [];
        $distroTmp = preg_split("/\n/", $buf, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($distroTmp as $info) {
            $parts = preg_split('/:/', $info, 2);
            if (isset($parts[0]) && isset($parts[1]) && trim($parts[1]) != '') {
                $distro[trim($parts[0])] = trim($parts[1]);
            }
        }

        if (!isset($distro['Distributor ID']) && !isset($distro['Description'])) {
            if (isset($distroTmp[0]) && trim($distroTmp[0]) != '') {
                $this->info->setDistribution(trim($distroTmp[0]));
                if (preg_match('/^(\S+)\s*/', $distroTmp[0], $m) && isset($list[trim($m[1])]['Image'])) {
                    $this->info->setDistributionIcon($list[trim($m[1])]['Image']);
                }
            }

            return;
        }

        if (isset($distro['Description']) && preg_match('/^NAME=\s*"?([^"\n]+)"?\s*$/', $distro['Description'], $m)) {
            $distro['Description'] = $m[1];
        }
        if (isset($distro['Description']) && ($distro['Description'] != 'n/a') && (!isset($distro['Distributor ID']) || (($distro['Distributor ID'] != 'n/a') && ($distro['Description'] != $distro['Distributor ID'])))) {
            $this->info->setDistribution($distro['Description']);
            if (isset($distro['Release']) && ($distro['Release'] != 'n/a') && ($distro['Release'] != $distro['Description']) && strstr($distro['Release'], '.')) {
                $tofind = preg_match("/^(\d+)\.[0]+$/", $distro['Release'], $m) ? $m[1] : $distro['Release'];
                if (!preg_match('/^' . $tofind . "[\s\.]|[\(\[]" . $tofind . "[\.\)\]]|\s" . $tofind . "$|\s" . $tofind . "[\s\.]/", $distro['Description'])) {
                    $this->info->setDistribution($this->info->getDistribution() . ' ' . $distro['Release']);
                }
            }
        } elseif (isset($distro['Distributor ID']) && ($distro['Distributor ID'] != 'n/a')) {
            $this->info->setDistribution($distro['Distributor ID']);
            if (isset($distro['Release']) && ($distro['Release'] != 'n/a')) {
                $this->info->setDistribution($this->info->getDistribution() . ' ' . $distro['Release']);
            }
            if (isset($distro['Codename']) && ($distro['Codename'] != 'n/a')) {
                $this->info->setDistribution($this->info->getDistribution() . ' (' . $distro['Codename'] . ')');
            }
        }
        if (isset($distro['Distributor ID']) && ($distro['Distributor ID'] != 'n/a') && isset($list[$distro['Distributor ID']]['Image'])) {
            $this->info->setDistributionIcon($list[$distro['Distributor ID']]['Image']);
        } elseif (isset($distro['Description']) && ($distro['Description'] != 'n/a') && isset($list[$distro['Description']]['Image'])) {
            $this->info->setDistribution($distro['Description']);
            $this->info->setDistributionIcon($list[$distro['Description']]['Image']);
        }
    }

    /**
     * @param array $list
     *
     * @return void
     */
    protected function parseDistroFromFiles($list) {
        $buf = '';
        if ($this->server->fileExists('/etc/os-release') && $this->server->rfts('/etc/os-release', $buf, 0, 4096, false) && preg_match('/^NAME="?([^"\n]+)"?/m', $buf, $idBuf)) {
            if (preg_match('/^PRETTY_NAME="?([^"\n]+)"?/m', $buf, $descBuf) && !preg_match('/\$/', $descBuf[1])) {
                $this->info->setDistribution(trim($descBuf[1]));
            } else {
                $name = isset($list[trim($idBuf[1])]['Name']) ? trim($list[trim($idBuf[1])]['Name']) : trim($idBuf[1]);
                $this->info->setDistribution($name);
                if (preg_match('/^VERSION="?([^"\n]+)"?/m', $buf, $versBuf)) {
                    $this->info->setDistribution($this->info->getDistribution() . ' ' . trim($versBuf[1]));
                } elseif (preg_match('/^VERSION_ID="?([^"\n]+)"?/m', $buf, $versBuf)) {
                    $this->info->setDistribution($this->info->getDistribution() . ' ' . trim($versBuf[1]));
                }
            }
            if (isset($list[trim($idBuf[1])]['Image'])) {
                $this->info->setDistributionIcon($list[trim($idBuf[1])]['Image']);
            }
        } elseif ($this->server->fileExists('/etc/lsb-release') && $this->server->rfts('/etc/lsb-release', $buf, 0, 4096, false) && preg_match('/^DISTRIB_ID="?([^"\n]+)"?/m', $buf, $idBuf)) {
            if (preg_match('/^DISTRIB_DESCRIPTION="?([^"\n]+)"?/m', $buf, $descBuf) && (trim($descBuf[1]) != trim($idBuf[1]))) {
                $this->info->setDistribution(trim($descBuf[1]));
            } else {
                $name = isset($list[trim($idBuf[1])]['Name']) ? trim($list[trim($idBuf[1])]['Name']) : trim($idBuf[1]);
                $this->info->setDistribution($name);
                if (preg_match('/^DISTRIB_RELEASE="?([^"\n]+)"?/m', $buf, $versBuf)) {
                    $this->info->setDistribution($this->info->getDistribution() . ' ' . trim($versBuf[1]));
                }
            }
            if (isset($list[trim($idBuf[1])]['Image'])) {
                $this->info->setDistributionIcon($list[trim($idBuf[1])]['Image']);
            }
        } elseif ($this->server->fileExists('/etc/debian_version')) {
            $this->server->rfts('/etc/debian_version', $buf, 1, 4096, false);
            $name = isset($list['Debian']['Name']) ? $list['Debian']['Name'] : 'Debian';
            $this->info->setDistribution(trim($buf) != '' ? $name . ' ' . trim($buf) : $name);
            if (isset($list['Debian']['Image'])) {
                $this->info->setDistributionIcon($list['Debian']['Image']);
            }
        }
    }

    /**
     * @return void
     */
    protected function buildUsers() {
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

    /**
     * @return void
     */
    protected function buildProcesses() {
        $buf = '';
        if ($this->server->executeProgram('ps', 'aux --no-headers 2>/dev/null', $buf) && strlen($buf) > 0) {
            $lines = preg_split("/\n/", trim($buf), -1, PREG_SPLIT_NO_EMPTY);
            $processes = [];
            $processes['*'] = count($lines);
            foreach ($lines as $line) {
                $parts = preg_split('/\s+/', trim($line));
                $state = isset($parts[7]) ? substr($parts[7], 0, 1) : ' ';
                if (isset($processes[$state])) {
                    $processes[$state]++;
                } else {
                    $processes[$state] = 1;
                }
            }
            $this->info->setProcesses($processes);
        }
    }

    /**
     * @return void
     */
    protected function buildLoadAvg() {
        $buf = '';
        if ($this->server->rfts('/proc/loadavg', $buf, 1)) {
            $result = preg_split("/\s/", $buf, 4);
            unset($result[3]);
            $this->info->setLoad(implode(' ', $result));
        } elseif ($this->server->executeProgram('uptime', '', $buf) && preg_match('/load average: (.*), (.*), (.*)$/', $buf, $m)) {
            $this->info->setLoad($m[1] . ' ' . $m[2] . ' ' . $m[3]);
        }
        if ($this->server->config()->isLoadPercentEnabled()) {
            $this->info->setLoadPercent($this->parseProcStat('cpu'));
        }
    }

    /**
     * @param string $cpuline
     *
     * @return float
     */
    protected function parseProcStat($cpuline) {
        if (is_null($this->cpuLoads)) {
            $this->cpuLoads = [];
            $cpuTmp = [];
            $buf = '';
            if ($this->server->rfts('/proc/stat', $buf)) {
                if (preg_match_all('/^(cpu[0-9]*) (.*)/m', $buf, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $line) {
                        $cpu = $line[1];
                        $ab = $ac = $ad = $ae = 0;
                        sscanf($line[2], '%Ld %Ld %Ld %Ld', $ab, $ac, $ad, $ae);
                        $cpuTmp[$cpu] = ['load' => $ab + $ac + $ad, 'total' => $ab + $ac + $ad + $ae];
                    }
                }
                sleep(1);
                if ($this->server->rfts('/proc/stat', $buf)) {
                    if (preg_match_all('/^(cpu[0-9]*) (.*)/m', $buf, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $line) {
                            $cpu = $line[1];
                            if (isset($cpuTmp[$cpu])) {
                                $ab = $ac = $ad = $ae = 0;
                                sscanf($line[2], '%Ld %Ld %Ld %Ld', $ab, $ac, $ad, $ae);
                                $load2 = $ab + $ac + $ad;
                                $total2 = $ab + $ac + $ad + $ae;
                                $load = $cpuTmp[$cpu]['load'];
                                $total = $cpuTmp[$cpu]['total'];
                                $this->cpuLoads[$cpu] = 0;
                                if ($total > 0 && $total2 > 0 && $load > 0 && $load2 > 0 && $total2 != $total && $load2 != $load) {
                                    $this->cpuLoads[$cpu] = (100 * ($load2 - $load)) / ($total2 - $total);
                                }
                            }
                        }
                    }
                }
            }
        }

        return isset($this->cpuLoads[$cpuline]) ? $this->cpuLoads[$cpuline] : 0;
    }

    /**
     * @return void
     */
    protected function buildMachine() {
        $buf = '';
        $machine = '';
        if (($this->server->rfts('/var/log/dmesg', $buf, 0, 4096, false) && preg_match('/^[\s\[\]\.\d]*DMI:\s*(.*)/m', $buf, $m))
            || ($this->server->executeProgram('dmesg', '', $buf, false) && preg_match('/^[\s\[\]\.\d]*DMI:\s*(.*)/m', $buf, $m))
        ) {
            $machine = trim($m[1]);
        } else {
            $parts = [];
            if ($this->server->rfts('/sys/devices/virtual/dmi/id/board_vendor', $buf, 1, 4096, false) && trim($buf) != '') {
                $machine = trim($buf);
            }
            if ($this->server->rfts('/sys/devices/virtual/dmi/id/product_name', $buf, 1, 4096, false) && trim($buf) != '') {
                $parts[] = trim($buf);
            }
            if ($this->server->rfts('/sys/devices/virtual/dmi/id/board_name', $buf, 1, 4096, false) && trim($buf) != '') {
                $parts[] = trim($buf);
            }
            $bios = '';
            if ($this->server->rfts('/sys/devices/virtual/dmi/id/bios_version', $buf, 1, 4096, false) && trim($buf) != '') {
                $bios = trim($buf);
            }
            if ($this->server->rfts('/sys/devices/virtual/dmi/id/bios_date', $buf, 1, 4096, false) && trim($buf) != '') {
                $bios = trim($bios . ' ' . trim($buf));
            }
            if (isset($parts[0])) {
                $machine .= ' ' . $parts[0];
            }
            if (isset($parts[1])) {
                $machine .= '/' . $parts[1];
            }
            if ($bios != '') {
                $machine .= ', BIOS ' . $bios;
            }
        }
        if ($machine != '') {
            $machine = trim(preg_replace("/^\/,?/", '', preg_replace("/ ?(To be filled by O\.E\.M\.|System manufacturer|System Product Name|Not Specified) ?/i", '', $machine)));
            $this->info->setMachine($machine);
        }
    }

    /**
     * @return void
     */
    protected function buildCpuInfo() {
        $buf = '';
        if (!$this->server->rfts('/proc/cpuinfo', $buf)) {
            return;
        }

        if (preg_match('/\nCpu(\d+)Bogo\s*:/i', $buf)) {
            $buf = preg_replace('/\nCpu(\d+)ClkTck\s*:/i', "\nCpu0ClkTck:", preg_replace('/\nCpu(\d+)Bogo\s*:/i', "\n\nprocessor: $1\nCpu0Bogo:", $buf));
        } else {
            $buf = preg_replace('/\nCpu(\d+)ClkTck\s*:/i', "\n\nprocessor: $1\nCpu0ClkTck:", $buf);
        }

        $processors = preg_split('/\s?\n\s?\n/', trim($buf));
        $procname = null;
        $cpuSpeed = null;

        foreach ($processors as $processor) {
            if (preg_match('/^\s*processor\s*:/mi', $processor)) {
                $dev = CServer_Factory::createDeviceCpu();
                $details = preg_split("/\n/", $processor, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($details as $detail) {
                    $parts = preg_split('/\s*:\s*/', trim($detail));
                    if (count($parts) == 2 && trim($parts[1]) !== '') {
                        $val = trim($parts[1]);
                        switch (strtolower($parts[0])) {
                            case 'processor':
                                if (is_numeric($val) && $procname !== null) {
                                    $dev->setModel($procname);
                                } elseif (!is_numeric($val)) {
                                    $procname = $val;
                                    $dev->setModel($val);
                                }

                                break;
                            case 'model name':
                            case 'cpu model':
                            case 'cpu type':
                            case 'cpu':
                                $dev->setModel($val);

                                break;
                            case 'cpu mhz':
                            case 'clock':
                                if ($val > 0) {
                                    $dev->setCpuSpeed($val);
                                    $cpuSpeed = true;
                                }

                                break;
                            case 'cpu0clktck':
                                $dev->setCpuSpeed(hexdec($val) / 1000000);
                                $cpuSpeed = true;

                                break;
                            case 'l3 cache':
                            case 'cache size':
                                $dev->setCache(trim(preg_replace('/[a-zA-Z]/', '', $val)) * 1024);

                                break;
                            case 'initial bogomips':
                            case 'bogomips':
                            case 'cpu0bogo':
                                $dev->setBogomips(round($val));

                                break;
                            case 'flags':
                                if (preg_match('/ vmx/', $val)) {
                                    $dev->setVirt('vmx');
                                } elseif (preg_match('/ svm/', $val)) {
                                    $dev->setVirt('svm');
                                } elseif (preg_match('/ hypervisor/', $val)) {
                                    $dev->setVirt('hypervisor');
                                }

                                break;
                        }
                    }
                }
                if ($dev->getModel() === '') {
                    $dev->setModel('unknown');
                }
                $this->info->addCpus($dev);
            }
        }
    }
}
