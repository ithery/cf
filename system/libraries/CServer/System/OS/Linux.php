<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 2:27:16 PM
 */
class CServer_System_OS_Linux extends CServer_System_OS {
    /**
     * Assoc array of all CPUs loads.
     */
    protected $cpuLoads;

    /**
     * Processor Load
     * optionally create a loadbar.
     *
     * @return void
     */
    public function buildLoadAvg() {
        $cmd = $this->createCommand();
        if ($cmd->rfts('/proc/loadavg', $buf, 1, 4096, CServer::getOS() != 'Android')) {
            $result = preg_split("/\s/", $buf, 4);
            // don't need the extra values, only first three
            unset($result[3]);
            $this->info->setLoad(implode(' ', $result));
        } elseif ($cmd->executeProgram('uptime', '', $buf) && preg_match('/load average: (.*), (.*), (.*)$/', $buf, $ar_buf)) {
            $this->info->setLoad($ar_buf[1] . ' ' . $ar_buf[2] . ' ' . $ar_buf[3]);
        }
        if (CServer::config()->isLoadPercentEnabled()) {
            $this->info->setLoadPercent($this->parseProcStat('cpu'));
        }
    }

    /**
     * Fill the load for a individual cpu, through parsing /proc/stat for the specified cpu.
     *
     * @param string $cpuline cpu for which load should be meassured
     *
     * @return int
     */
    protected function parseProcStat($cpuline) {
        $cmd = $this->createCommand();
        if (is_null($this->cpuLoads)) {
            $this->cpuLoads = [];

            $cpu_tmp = [];
            if ($cmd->rfts('/proc/stat', $buf)) {
                if (preg_match_all('/^(cpu[0-9]*) (.*)/m', $buf, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $line) {
                        $cpu = $line[1];
                        $buf2 = $line[2];

                        $cpu_tmp[$cpu] = [];

                        $ab = 0;
                        $ac = 0;
                        $ad = 0;
                        $ae = 0;
                        sscanf($buf2, '%Ld %Ld %Ld %Ld', $ab, $ac, $ad, $ae);
                        $cpu_tmp[$cpu]['load'] = $ab + $ac + $ad; // cpu.user + cpu.sys
                        $cpu_tmp[$cpu]['total'] = $ab + $ac + $ad + $ae; // cpu.total
                    }
                }

                // we need a second value, wait 1 second befor getting (< 1 second no good value will occour)
                sleep(1);

                if ($cmd->rfts('/proc/stat', $buf)) {
                    if (preg_match_all('/^(cpu[0-9]*) (.*)/m', $buf, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $line) {
                            $cpu = $line[1];
                            if (isset($cpu_tmp[$cpu])) {
                                $buf2 = $line[2];

                                $ab = 0;
                                $ac = 0;
                                $ad = 0;
                                $ae = 0;
                                sscanf($buf2, '%Ld %Ld %Ld %Ld', $ab, $ac, $ad, $ae);
                                $load2 = $ab + $ac + $ad; // cpu.user + cpu.sys
                                $total2 = $ab + $ac + $ad + $ae; // cpu.total
                                $total = $cpu_tmp[$cpu]['total'];
                                $load = $cpu_tmp[$cpu]['load'];
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

        if (isset($this->cpuLoads[$cpuline])) {
            return $this->cpuLoads[$cpuline];
        } else {
            return 0;
        }
    }

    /**
     * Get the information of machine.
     *
     * @see CServer_System_OSInterface::buildMachine()
     *
     * @return void
     */
    public function buildMachine() {
        $cmd = $this->createCommand();
        $machine = '';
        if (($cmd->rfts('/var/log/dmesg', $result, 0, 4096, false) && preg_match('/^[\s\[\]\.\d]*DMI:\s*(.*)/m', $result, $ar_buf)) || ($cmd->executeProgram('dmesg', '', $result, false) && preg_match('/^[\s\[\]\.\d]*DMI:\s*(.*)/m', $result, $ar_buf))) {
            $machine = trim($ar_buf[1]);
        } else { //data from /sys/devices/virtual/dmi/id/
            $product = '';
            $board = '';
            $bios = '';
            if ($cmd->rfts('/sys/devices/virtual/dmi/id/board_vendor', $buf, 1, 4096, false) && (trim($buf) != '')) {
                $machine = trim($buf);
            }
            if ($cmd->rfts('/sys/devices/virtual/dmi/id/product_name', $buf, 1, 4096, false) && (trim($buf) != '')) {
                $product = trim($buf);
            }
            if ($cmd->rfts('/sys/devices/virtual/dmi/id/board_name', $buf, 1, 4096, false) && (trim($buf) != '')) {
                $board = trim($buf);
            }
            if ($cmd->rfts('/sys/devices/virtual/dmi/id/bios_version', $buf, 1, 4096, false) && (trim($buf) != '')) {
                $bios = trim($buf);
            }
            if ($cmd->rfts('/sys/devices/virtual/dmi/id/bios_date', $buf, 1, 4096, false) && (trim($buf) != '')) {
                $bios = trim($bios . ' ' . trim($buf));
            }
            if ($product != '') {
                $machine .= ' ' . $product;
            }
            if ($board != '') {
                $machine .= '/' . $board;
            }
            if ($bios != '') {
                $machine .= ', BIOS ' . $bios;
            }
        }

        if ($machine != '') {
            $machine = trim(preg_replace("/^\/,?/", '', preg_replace("/ ?(To be filled by O\.E\.M\.|System manufacturer|System Product Name|Not Specified) ?/i", '', $machine)));
        }

        if ($cmd->fileexists($filename = '/etc/config/uLinux.conf') // QNAP detection
            && $cmd->rfts($filename, $buf, 0, 4096, false) && preg_match("/^Rsync\sModel\s*=\s*QNAP/m", $buf) && $cmd->fileexists($filename = '/etc/platform.conf') // Platform detection
            && $cmd->rfts($filename, $buf, 0, 4096, false) && preg_match("/^DISPLAY_NAME\s*=\s*(\S+)/m", $buf, $mach_buf) && ($mach_buf[1] !== '')
        ) {
            if ($machine != '') {
                $machine = 'QNAP ' . $mach_buf[1] . ' - ' . $machine;
            } else {
                $machine = 'QNAP ' . $mach_buf[1];
            }
        }

        if ($machine != '') {
            $this->info->setMachine($machine);
        }
    }

    /**
     * Get the information of kernel.
     *
     * @see CServer_System_OSInterface::buildKernel()
     *
     * @return void
     */
    public function buildKernel() {
        $cmd = $this->createCommand();
        $cfg = CServer::config();
        $result = '';
        if ($cmd->executeProgram($uname = 'uptrack-uname', '-r', $strBuf, false) // show effective kernel if ksplice uptrack is installed
            || $cmd->executeProgram($uname = 'uname', '-r', $strBuf, $cfg->isDebug())
        ) {
            $result = $strBuf;
            if ($cmd->executeProgram($uname, '-v', $strBuf, $cfg->isDebug())) {
                if (preg_match('/SMP/', $strBuf)) {
                    $result .= ' (SMP)';
                }
            }
            if ($cmd->executeProgram($uname, '-m', $strBuf, $cfg->isDebug())) {
                $result .= ' ' . $strBuf;
            }
        } elseif ($cmd->rfts('/proc/version', $strBuf, 1) && preg_match('/version\s+(\S+)/', $strBuf, $ar_buf)) {
            $result = $ar_buf[1];
            if (preg_match('/SMP/', $strBuf)) {
                $result .= ' (SMP)';
            }
        }
        if ($result != '') {
            if ($cmd->rfts('/proc/self/cgroup', $strBuf2, 0, 4096, false)) {
                if (preg_match('/:\/lxc\//m', $strBuf2)) {
                    $result .= ' [lxc]';
                } elseif (preg_match('/:\/docker\//m', $strBuf2)) {
                    $result .= ' [docker]';
                } elseif (preg_match('/:\/system\.slice\/docker\-/m', $strBuf2)) {
                    $result .= ' [docker]';
                }
            }
            if ($cmd->rfts('/proc/version', $strBuf2, 1, 4096, false) && preg_match('/^Linux version [\d\.-]+-Microsoft/', $strBuf2)) {
                $result .= ' [lxss]';
            }
            $this->info->setKernel($result);
        }
    }

    /**
     * UpTime
     * time the system is running.
     *
     * @return void
     */
    public function buildUptime() {
        $cmd = $this->createCommand();
        if ($cmd->rfts('/proc/uptime', $buf, 1, 4096, CServer::getOS() != 'Android')) {
            $ar_buf = preg_split('/ /', $buf);
            $this->info->setUptime(trim($ar_buf[0]));
        } elseif ($cmd->executeProgram('uptime', '', $buf)) {
            if (preg_match("/up (\d+) day[s]?,[ ]+(\d+):(\d+),/", $buf, $ar_buf)) {
                $min = $ar_buf[3];
                $hours = $ar_buf[2];
                $days = $ar_buf[1];
                $this->info->setUptime($days * 86400 + $hours * 3600 + $min * 60);
            } elseif (preg_match("/up (\d+) day[s]?,[ ]+(\d+) min,/", $buf, $ar_buf)) {
                $min = $ar_buf[2];
                $days = $ar_buf[1];
                $this->info->setUptime($days * 86400 + $min * 60);
            } elseif (preg_match("/up[ ]+(\d+):(\d+),/", $buf, $ar_buf)) {
                $min = $ar_buf[2];
                $hours = $ar_buf[1];
                $this->info->setUptime($hours * 3600 + $min * 60);
            } elseif (preg_match("/up[ ]+(\d+) min,/", $buf, $ar_buf)) {
                $min = $ar_buf[1];
                $this->info->setUptime($min * 60);
            }
        }
    }

    /**
     * Get the information of distro.
     *
     * @see CServer_System_OSInterface::buildDistro()
     *
     * @return void
     */
    public function buildDistro() {
        $cmd = $this->createCommand();
        $this->info->setDistribution('Linux');
        $list = CServer_Const::$distros;

        if (!$list) {
            return;
        }
        // We have the '2>/dev/null' because Ubuntu gives an error on this command which causes the distro to be unknown
        if ($cmd->executeProgram('lsb_release', '-a 2>/dev/null', $distro_info, CServer::config()->isDebug()) && (strlen($distro_info) > 0)) {
            $distro_tmp = preg_split("/\n/", $distro_info, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($distro_tmp as $info) {
                $info_tmp = preg_split('/:/', $info, 2);
                if (isset($distro_tmp[0]) && !is_null($distro_tmp[0]) && (trim($distro_tmp[0]) != '')
                    && isset($distro_tmp[1]) && !is_null($distro_tmp[1]) && (trim($distro_tmp[1]) != '')
                ) {
                    $distro[trim($info_tmp[0])] = trim($info_tmp[1]);
                }
            }
            if (!isset($distro['Distributor ID']) && !isset($distro['Description'])) { // Systems like StartOS
                if (isset($distro_tmp[0]) && !is_null($distro_tmp[0]) && (trim($distro_tmp[0]) != '')) {
                    $this->info->setDistribution(trim($distro_tmp[0]));
                    if (preg_match('/^(\S+)\s*/', $distro_tmp[0], $id_buf) && isset($list[trim($id_buf[1])]['Image'])) {
                        $this->info->setDistributionIcon($list[trim($id_buf[1])]['Image']);
                    }
                }
            } else {
                if (isset($distro['Description']) && preg_match('/^NAME=\s*"?([^"\n]+)"?\s*$/', $distro['Description'], $name_tmp)) {
                    $distro['Description'] = $name_tmp[1];
                }
                if (isset($distro['Description']) && ($distro['Description'] != 'n/a') && (!isset($distro['Distributor ID']) || (($distro['Distributor ID'] != 'n/a') && ($distro['Description'] != $distro['Distributor ID'])))) {
                    $this->info->setDistribution($distro['Description']);
                    if (isset($distro['Release']) && ($distro['Release'] != 'n/a') && ($distro['Release'] != $distro['Description']) && strstr($distro['Release'], '.')) {
                        if (preg_match("/^(\d+)\.[0]+$/", $distro['Release'], $match_buf)) {
                            $tofind = $match_buf[1];
                        } else {
                            $tofind = $distro['Release'];
                        }
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
                } elseif (isset($distro['Description']) && ($distro['Description'] != 'n/a')) {
                    $this->info->setDistribution($distro['Description']);
                    if (isset($list[$distro['Description']]['Image'])) {
                        $this->info->setDistributionIcon($list[$distro['Description']]['Image']);
                    }
                }
            }
        } else {
            /* default error handler */
            if (function_exists('errorHandlerPsi')) {
                restore_error_handler();
            }
            /* fatal errors only */
            $old_err_rep = error_reporting();
            error_reporting(E_ERROR);

            // Fall back in case 'lsb_release' does not exist but exist /etc/lsb-release
            if ($cmd->fileExists($filename = '/etc/lsb-release') && $cmd->rfts($filename, $buf, 0, 4096, false) && preg_match('/^DISTRIB_ID="?([^"\n]+)"?/m', $buf, $id_buf)) {
                if (preg_match('/^DISTRIB_DESCRIPTION="?([^"\n]+)"?/m', $buf, $desc_buf) && (trim($desc_buf[1]) != trim($id_buf[1]))) {
                    $this->info->setDistribution(trim($desc_buf[1]));
                    if (preg_match('/^DISTRIB_RELEASE="?([^"\n]+)"?/m', $buf, $vers_buf) && (trim($vers_buf[1]) != trim($desc_buf[1])) && strstr($vers_buf[1], '.')) {
                        if (preg_match("/^(\d+)\.[0]+$/", trim($vers_buf[1]), $match_buf)) {
                            $tofind = $match_buf[1];
                        } else {
                            $tofind = trim($vers_buf[1]);
                        }
                        if (!preg_match('/^' . $tofind . "[\s\.]|[\(\[]" . $tofind . "[\.\)\]]|\s" . $tofind . "$|\s" . $tofind . "[\s\.]/", trim($desc_buf[1]))) {
                            $this->info->setDistribution($this->info->getDistribution() . ' ' . trim($vers_buf[1]));
                        }
                    }
                } else {
                    if (isset($list[trim($id_buf[1])]['Name'])) {
                        $this->info->setDistribution(trim($list[trim($id_buf[1])]['Name']));
                    } else {
                        $this->info->setDistribution(trim($id_buf[1]));
                    }
                    if (preg_match('/^DISTRIB_RELEASE="?([^"\n]+)"?/m', $buf, $vers_buf)) {
                        $this->info->setDistribution($this->info->getDistribution() . ' ' . trim($vers_buf[1]));
                    }
                    if (preg_match('/^DISTRIB_CODENAME="?([^"\n]+)"?/m', $buf, $vers_buf)) {
                        $this->info->setDistribution($this->info->getDistribution() . ' (' . trim($vers_buf[1]) . ')');
                    }
                }
                if (isset($list[trim($id_buf[1])]['Image'])) {
                    $this->info->setDistributionIcon($list[trim($id_buf[1])]['Image']);
                }
            } else { // otherwise find files specific for distribution
                foreach ($list as $section => $distribution) {
                    if (!isset($distribution['Files'])) {
                        continue;
                    } else {
                        foreach (preg_split('/;/', $distribution['Files'], -1, PREG_SPLIT_NO_EMPTY) as $filename) {
                            if ($cmd->fileExists($filename)) {
                                $distro = $distribution;
                                if (isset($distribution['Mode']) && (strtolower($distribution['Mode']) == 'detection')) {
                                    $buf = '';
                                } elseif (isset($distribution['Mode']) && (strtolower($distribution['Mode']) == 'execute')) {
                                    if (!$cmd->executeProgram($filename, '2>/dev/null', $buf, CServer::config()->isDebug())) {
                                        $buf = '';
                                    }
                                } else {
                                    if (!$cmd->rfts($filename, $buf, 1, 4096, false)) {
                                        $buf = '';
                                    } elseif (isset($distribution['Mode']) && (strtolower($distribution['Mode']) == 'analyse')) {
                                        if (preg_match('/^(\S+)\s*/', preg_replace('/^Red\s+/', 'Red', $buf), $id_buf) && isset($list[trim($id_buf[1])]['Image'])) {
                                            $distro = $list[trim($id_buf[1])];
                                        }
                                    }
                                }
                                if (isset($distro['Image'])) {
                                    $this->info->setDistributionIcon($distro['Image']);
                                }
                                if (isset($distribution['Name'])) {
                                    if (is_null($buf) || (trim($buf) == '')) {
                                        $this->info->setDistribution($distribution['Name']);
                                    } else {
                                        $this->info->setDistribution($distribution['Name'] . ' ' . trim($buf));
                                    }
                                } else {
                                    if (is_null($buf) || (trim($buf) == '')) {
                                        $this->info->setDistribution($section);
                                    } else {
                                        $this->info->setDistribution(trim($buf));
                                    }
                                }
                                if (isset($distribution['Files2'])) {
                                    foreach (preg_split('/;/', $distribution['Files2'], -1, PREG_SPLIT_NO_EMPTY) as $filename2) {
                                        if ($cmd->fileExists($filename2) && $cmd->rfts($filename2, $buf, 0, 4096, false)) {
                                            if (preg_match('/^majorversion="?([^"\n]+)"?/m', $buf, $maj_buf) && preg_match('/^minorversion="?([^"\n]+)"?/m', $buf, $min_buf)) {
                                                $distr2 = $maj_buf[1] . '.' . $min_buf[1];
                                                if (preg_match('/^buildphase="?([^"\n]+)"?/m', $buf, $pha_buf) && ($pha_buf[1] !== '0')) {
                                                    $distr2 .= '.' . $pha_buf[1];
                                                }
                                                if (preg_match('/^buildnumber="?([^"\n]+)"?/m', $buf, $num_buf)) {
                                                    $distr2 .= '-' . $num_buf[1];
                                                }
                                                if (preg_match('/^builddate="?([^"\n]+)"?/m', $buf, $dat_buf)) {
                                                    $distr2 .= ' (' . $dat_buf[1] . ')';
                                                }
                                                $this->info->setDistribution($this->info->getDistribution() . ' ' . $distr2);
                                            } else {
                                                $distr2 = trim(substr($buf, 0, strpos($buf, "\n")));
                                                if (!is_null($distr2) && ($distr2 != '')) {
                                                    $this->info->setDistribution($this->info->getDistribution() . ' ' . $distr2);
                                                }
                                            }

                                            break;
                                        }
                                    }
                                }

                                break 2;
                            }
                        }
                    }
                }
            }
            // if the distribution is still unknown
            if ($this->info->getDistribution() == 'Linux') {
                if ($cmd->fileExists($filename = '/etc/DISTRO_SPECS') && $cmd->rfts($filename, $buf, 0, 4096, false) && preg_match('/^DISTRO_NAME=\'(.+)\'/m', $buf, $id_buf)) {
                    if (isset($list[trim($id_buf[1])]['Name'])) {
                        $dist = trim($list[trim($id_buf[1])]['Name']);
                    } else {
                        $dist = trim($id_buf[1]);
                    }
                    if (preg_match('/^DISTRO_VERSION=(.+)/m', $buf, $vers_buf)) {
                        $this->info->setDistribution(trim($dist . ' ' . trim($vers_buf[1])));
                    } else {
                        $this->info->setDistribution($dist);
                    }
                    if (isset($list[trim($id_buf[1])]['Image'])) {
                        $this->info->setDistributionIcon($list[trim($id_buf[1])]['Image']);
                    } else {
                        if (isset($list['Puppy']['Image'])) {
                            $this->info->setDistributionIcon($list['Puppy']['Image']);
                        }
                    }
                } elseif (($cmd->fileExists($filename = '/etc/distro-release') && $cmd->rfts($filename, $buf, 1, 4096, false) && !is_null($buf) && (trim($buf) != '')) || ($cmd->fileExists($filename = '/etc/system-release') && $cmd->rfts($filename, $buf, 1, 4096, false) && !is_null($buf) && (trim($buf) != ''))) {
                    $this->info->setDistribution(trim($buf));
                    if (preg_match('/^(\S+)\s*/', preg_replace('/^Red\s+/', 'Red', $buf), $id_buf) && isset($list[trim($id_buf[1])]['Image'])) {
                        $this->info->setDistributionIcon($list[trim($id_buf[1])]['Image']);
                    }
                } elseif ($cmd->fileExists($filename = '/etc/solydxk/info') && $cmd->rfts($filename, $buf, 0, 4096, false) && preg_match('/^DISTRIB_ID="?([^"\n]+)"?/m', $buf, $id_buf)) {
                    if (preg_match('/^DESCRIPTION="?([^"\n]+)"?/m', $buf, $desc_buf) && (trim($desc_buf[1]) != trim($id_buf[1]))) {
                        $this->info->setDistribution(trim($desc_buf[1]));
                    } else {
                        if (isset($list[trim($id_buf[1])]['Name'])) {
                            $this->info->setDistribution(trim($list[trim($id_buf[1])]['Name']));
                        } else {
                            $this->info->setDistribution(trim($id_buf[1]));
                        }
                        if (preg_match('/^RELEASE="?([^"\n]+)"?/m', $buf, $vers_buf)) {
                            $this->info->setDistribution($this->info->getDistribution() . ' ' . trim($vers_buf[1]));
                        }
                        if (preg_match('/^CODENAME="?([^"\n]+)"?/m', $buf, $vers_buf)) {
                            $this->info->setDistribution($this->info->getDistribution() . ' (' . trim($vers_buf[1]) . ')');
                        }
                    }
                    if (isset($list[trim($id_buf[1])]['Image'])) {
                        $this->info->setDistributionIcon($list[trim($id_buf[1])]['Image']);
                    } else {
                        $this->info->setDistributionIcon($list['SolydXK']['Image']);
                    }
                } elseif ($cmd->fileExists($filename = '/etc/os-release') && $cmd->rfts($filename, $buf, 0, 4096, false) && (preg_match('/^TAILS_VERSION_ID="?([^"\n]+)"?/m', $buf, $tid_buf) || preg_match('/^NAME="?([^"\n]+)"?/m', $buf, $id_buf))) {
                    if (preg_match('/^TAILS_VERSION_ID="?([^"\n]+)"?/m', $buf, $tid_buf)) {
                        if (preg_match('/^TAILS_PRODUCT_NAME="?([^"\n]+)"?/m', $buf, $desc_buf)) {
                            $this->info->setDistribution(trim($desc_buf[1]) . ' ' . trim($tid_buf[1]));
                        } else {
                            if (isset($list['Tails']['Name'])) {
                                $this->info->setDistribution(trim($list['Tails']['Name']) . ' ' . trim($tid_buf[1]));
                            } else {
                                $this->info->setDistribution('Tails' . ' ' . trim($tid_buf[1]));
                            }
                        }
                        $this->info->setDistributionIcon($list['Tails']['Image']);
                    } else {
                        if (preg_match('/^PRETTY_NAME="?([^"\n]+)"?/m', $buf, $desc_buf) && !preg_match('/\$/', $desc_buf[1])) { //if is not defined by variable
                            $this->info->setDistribution(trim($desc_buf[1]));
                        } else {
                            if (isset($list[trim($id_buf[1])]['Name'])) {
                                $this->info->setDistribution(trim($list[trim($id_buf[1])]['Name']));
                            } else {
                                $this->info->setDistribution(trim($id_buf[1]));
                            }
                            if (preg_match('/^VERSION="?([^"\n]+)"?/m', $buf, $vers_buf)) {
                                $this->info->setDistribution($this->info->getDistribution() . ' ' . trim($vers_buf[1]));
                            } elseif (preg_match('/^VERSION_ID="?([^"\n]+)"?/m', $buf, $vers_buf)) {
                                $this->info->setDistribution($this->info->getDistribution() . ' ' . trim($vers_buf[1]));
                            }
                        }
                        if (isset($list[trim($id_buf[1])]['Image'])) {
                            $this->info->setDistributionIcon($list[trim($id_buf[1])]['Image']);
                        }
                    }
                } elseif ($cmd->fileExists($filename = '/etc/debian_version')) {
                    if (!$cmd->rfts($filename, $buf, 1, 4096, false)) {
                        $buf = '';
                    }
                    if (isset($list['Debian']['Image'])) {
                        $this->info->setDistributionIcon($list['Debian']['Image']);
                    }
                    if (isset($list['Debian']['Name'])) {
                        if (is_null($buf) || (trim($buf) == '')) {
                            $this->info->setDistribution($list['Debian']['Name']);
                        } else {
                            $this->info->setDistribution($list['Debian']['Name'] . ' ' . trim($buf));
                        }
                    } else {
                        if (is_null($buf) || (trim($buf) == '')) {
                            $this->info->setDistribution('Debian');
                        } else {
                            $this->info->setDistribution(trim($buf));
                        }
                    }
                } elseif ($cmd->fileExists($filename = '/etc/config/uLinux.conf') && $cmd->rfts($filename, $buf, 0, 4096, false) && preg_match("/^Rsync\sModel\s*=\s*QNAP/m", $buf) && preg_match("/^Version\s*=\s*([\d\.]+)\r?\nBuild\sNumber\s*=\s*(\S+)/m", $buf, $ver_buf)) {
                    $buf = $ver_buf[1] . '-' . $ver_buf[2];
                    if (isset($list['QTS']['Image'])) {
                        $this->info->setDistributionIcon($list['QTS']['Image']);
                    }
                    if (isset($list['QTS']['Name'])) {
                        $this->info->setDistribution($list['QTS']['Name'] . ' ' . trim($buf));
                    } else {
                        $this->info->setDistribution(trim($buf));
                    }
                }
            }
            /* restore error level */
            error_reporting($old_err_rep);
            /* restore error handler */
            if (function_exists('errorHandlerPsi')) {
                set_error_handler('errorHandlerPsi');
            }
        }
    }

    /**
     * Processes.
     *
     * @return void
     */
    public function buildProcesses() {
        $cmd = $this->createCommand();
        $process = glob('/proc/*/status', GLOB_NOSORT);
        if (is_array($process) && (($total = count($process)) > 0)) {
            $processes['*'] = 0;
            $buf = '';
            for ($i = 0; $i < $total; $i++) {
                if ($cmd->rfts($process[$i], $buf, 0, 4096, false)) {
                    $processes['*']++; //current total
                    if (preg_match('/^State:\s+(\w)/m', $buf, $state)) {
                        if (isset($processes[$state[1]])) {
                            $processes[$state[1]]++;
                        } else {
                            $processes[$state[1]] = 1;
                        }
                    }
                }
            }
            if (!($processes['*'] > 0)) {
                $processes['*'] = $processes[' '] = $total; //all unknown
            }
            $this->info->setProcesses($processes);
        }
    }

    /**
     * Get the information of hostname.
     *
     * @see CServer_System_OSInterface::buildHostname()
     *
     * @return void
     */
    public function buildHostname() {
        $cmd = $this->createCommand();
        if (CServer::config()->isUseVHost()) {
            if ($cmd->readEnv('SERVER_NAME', $hnm)) {
                $this->info->setHostname($hnm);
            }
        } else {
            if ($cmd->rfts('/proc/sys/kernel/hostname', $result, 1, 4096, CServer::getOS() != 'Android')) {
                $result = trim($result);
                $ip = gethostbyname($result);
                if ($ip != $result) {
                    $this->info->setHostname(gethostbyaddr($ip));
                } else {
                    $this->info->setHostname($result);
                }
            } elseif ($cmd->executeProgram('hostname', '', $ret)) {
                $this->info->setHostname($ret);
            }
        }
    }

    /**
     * CPU information
     * All of the tags here are highly architecture dependant.
     *
     * @return void
     */
    public function buildCpuInfo() {
        $cmd = $this->createCommand();
        if ($cmd->rfts('/proc/cpuinfo', $bufr)) {
            $cpulist = null;
            $raslist = null;

            // sparc
            if (preg_match('/\nCpu(\d+)Bogo\s*:/i', $bufr)) {
                $bufr = preg_replace('/\nCpu(\d+)ClkTck\s*:/i', "\nCpu0ClkTck:", preg_replace('/\nCpu(\d+)Bogo\s*:/i', "\n\nprocessor: $1\nCpu0Bogo:", $bufr));
            } else {
                $bufr = preg_replace('/\nCpu(\d+)ClkTck\s*:/i', "\n\nprocessor: $1\nCpu0ClkTck:", $bufr);
            }

            $processors = preg_split('/\s?\n\s?\n/', trim($bufr));

            //first stage
            $_arch = null;
            $_impl = null;
            $_part = null;
            $_hard = null;
            $_revi = null;
            $_cpus = null;
            $_buss = null;
            $procname = null;
            foreach ($processors as $processor) {
                if (!preg_match('/^\s*processor\s*:/mi', $processor)) {
                    $details = preg_split("/\n/", $processor, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($details as $detail) {
                        $arrBuff = preg_split('/\s*:\s*/', trim($detail));
                        if ((count($arrBuff) == 2) && (($arrBuff1 = trim($arrBuff[1])) !== '')) {
                            switch (strtolower($arrBuff[0])) {
                                case 'cpu architecture':
                                    $_arch = $arrBuff1;

                                    break;
                                case 'cpu implementer':
                                    $_impl = $arrBuff1;

                                    break;
                                case 'cpu part':
                                    $_part = $arrBuff1;

                                    break;
                                case 'hardware':
                                    $_hard = $arrBuff1;

                                    break;
                                case 'revision':
                                    $_revi = $arrBuff1;

                                    break;
                                case 'cpu frequency':
                                    if (preg_match('/^(\d+)\s+Hz/i', $arrBuff1, $bufr2)) {
                                        $_cpus = round($bufr2[1] / 1000000);
                                    }

                                    break;
                                case 'system bus frequency':
                                    if (preg_match('/^(\d+)\s+Hz/i', $arrBuff1, $bufr2)) {
                                        $_buss = round($bufr2[1] / 1000000);
                                    }

                                    break;
                                case 'cpu':
                                    $procname = $arrBuff1;

                                    break;
                            }
                        }
                    }
                }
            }

            //second stage
            $cpucount = 0;
            $speedset = false;
            foreach ($processors as $processor) {
                if (preg_match('/^\s*processor\s*:/mi', $processor)) {
                    $proc = null;
                    $arch = null;
                    $impl = null;
                    $part = null;
                    $dev = CServer_Factory::createDeviceCpu();
                    $details = preg_split("/\n/", $processor, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($details as $detail) {
                        $arrBuff = preg_split('/\s*:\s*/', trim($detail));
                        if ((count($arrBuff) == 2) && (($arrBuff1 = trim($arrBuff[1])) !== '')) {
                            switch (strtolower($arrBuff[0])) {
                                case 'processor':
                                    $proc = $arrBuff1;
                                    if (is_numeric($proc)) {
                                        if (strlen($procname) > 0) {
                                            $dev->setModel($procname);
                                        }
                                    } else {
                                        $procname = $proc;
                                        $dev->setModel($procname);
                                    }

                                    break;
                                case 'model name':
                                case 'cpu model':
                                case 'cpu type':
                                case 'cpu':
                                    $dev->setModel($arrBuff1);

                                    break;
                                case 'cpu mhz':
                                case 'clock':
                                    if ($arrBuff1 > 0) { //openSUSE fix
                                        $dev->setCpuSpeed($arrBuff1);
                                        $speedset = true;
                                    }

                                    break;
                                case 'cycle frequency [hz]':
                                    $dev->setCpuSpeed($arrBuff1 / 1000000);
                                    $speedset = true;

                                    break;
                                case 'cpu0clktck':
                                    $dev->setCpuSpeed(hexdec($arrBuff1) / 1000000); // Linux sparc64
                                    $speedset = true;

                                    break;
                                case 'l3 cache':
                                case 'cache size':
                                    $dev->setCache(trim(preg_replace('/[a-zA-Z]/', '', $arrBuff1)) * 1024);

                                    break;
                                case 'initial bogomips':
                                case 'bogomips':
                                case 'cpu0bogo':
                                    $dev->setBogomips(round($arrBuff1));

                                    break;
                                case 'flags':
                                    if (preg_match('/ vmx/', $arrBuff1)) {
                                        $dev->setVirt('vmx');
                                    } elseif (preg_match('/ svm/', $arrBuff1)) {
                                        $dev->setVirt('svm');
                                    } elseif (preg_match('/ hypervisor/', $arrBuff1)) {
                                        $dev->setVirt('hypervisor');
                                    }

                                    break;
                                case 'i size':
                                case 'd size':
                                    if ($dev->getCache() === null) {
                                        $dev->setCache($arrBuff1 * 1024);
                                    } else {
                                        $dev->setCache($dev->getCache() + ($arrBuff1 * 1024));
                                    }

                                    break;
                                case 'cpu architecture':
                                    $arch = $arrBuff1;

                                    break;
                                case 'cpu implementer':
                                    $impl = $arrBuff1;

                                    break;
                                case 'cpu part':
                                    $part = $arrBuff1;

                                    break;
                            }
                        }
                    }
                    if ($arch === null) {
                        $arch = $_arch;
                    }
                    if ($impl === null) {
                        $impl = $_impl;
                    }
                    if ($part === null) {
                        $part = $_part;
                    }

                    // sparc64 specific code follows
                    // This adds the ability to display the cache that a CPU has
                    // Originally made by Sven Blumenstein <bazik@gentoo.org> in 2004
                    // Modified by Tom Weustink <freshy98@gmx.net> in 2004
                    $sparclist = ['SUNW,UltraSPARC@0,0', 'SUNW,UltraSPARC-II@0,0', 'SUNW,UltraSPARC@1c,0', 'SUNW,UltraSPARC-IIi@1c,0', 'SUNW,UltraSPARC-II@1c,0', 'SUNW,UltraSPARC-IIe@0,0'];
                    foreach ($sparclist as $name) {
                        if ($cmd->rfts('/proc/openprom/' . $name . '/ecache-size', $buf, 1, 32, false)) {
                            $dev->setCache(base_convert(trim($buf), 16, 10));
                        }
                    }
                    // sparc64 specific code ends
                    // XScale detection code
                    if (($arch === '5TE') && ($dev->getBogomips() != null)) {
                        $dev->setCpuSpeed($dev->getBogomips()); //BogoMIPS are not BogoMIPS on this CPU, it's the speed
                        $speedset = true;
                        $dev->setBogomips(null); // no BogoMIPS available, unset previously set BogoMIPS
                    }

                    if (($dev->getBusSpeed() == 0) && ($_buss !== null)) {
                        $dev->setBusSpeed($_buss);
                    }
                    if (($dev->getCpuSpeed() == 0) && ($_cpus !== null)) {
                        $dev->setCpuSpeed($_cpus);
                        $speedset = true;
                    }

                    if ($proc != null) {
                        if (!is_numeric($proc)) {
                            $proc = 0;
                        }
                        // variable speed processors specific code follows
                        if ($cmd->rfts('/sys/devices/system/cpu/cpu' . $proc . '/cpufreq/cpuinfo_cur_freq', $buf, 1, 4096, false)) {
                            $dev->setCpuSpeed(trim($buf) / 1000);
                            $speedset = true;
                        } elseif ($cmd->rfts('/sys/devices/system/cpu/cpu' . $proc . '/cpufreq/scaling_cur_freq', $buf, 1, 4096, false)) {
                            $dev->setCpuSpeed(trim($buf) / 1000);
                            $speedset = true;
                        }
                        if ($cmd->rfts('/sys/devices/system/cpu/cpu' . $proc . '/cpufreq/cpuinfo_max_freq', $buf, 1, 4096, false)) {
                            $dev->setCpuSpeedMax(trim($buf) / 1000);
                        }
                        if ($cmd->rfts('/sys/devices/system/cpu/cpu' . $proc . '/cpufreq/cpuinfo_min_freq', $buf, 1, 4096, false)) {
                            $dev->setCpuSpeedMin(trim($buf) / 1000);
                        }
                        // variable speed processors specific code ends
                        if (CServer::config()->isLoadPercentEnabled()) {
                            $dev->setLoad($this->parseProcStat('cpu' . $proc));
                        }
                        /*
                          if ($cmd->rfts('/proc/acpi/thermal_zone/THRM/temperature', $buf, 1, 4096, false)
                          &&  preg_match("/(\S+)\sC$/", $buf, $value)) {
                          $dev->setTemp(value[1]);
                          }
                         */
                        if (($arch !== null) && ($impl !== null) && ($part !== null)) {
                            if (($impl === '0x41') && (($_hard === 'BCM2708') || ($_hard === 'BCM2835') || ($_hard === 'BCM2709') || ($_hard === 'BCM2836') || ($_hard === 'BCM2710') || ($_hard === 'BCM2837')) && ($_revi !== null)) { // Raspberry Pi detection (instead of 'cat /proc/device-tree/model')
                                if ($raslist === null) {
                                    $raslist = @parse_ini_file(DOCROOT . 'system/data/server/raspberry.ini', true);
                                }
                                if ($raslist && !preg_match('/[^0-9a-f]/', $_revi)) {
                                    $revidec = hexdec($_revi);
                                    if (($revidec) & 0x800000) {
                                        if ($this->info->getMachine() === '') {
                                            $manufacturer = ($revidec >> 16) & 15;
                                            if (isset($raslist['manufacturer'][$manufacturer])) {
                                                $manuf = ' ' . $raslist['manufacturer'][$manufacturer];
                                            } else {
                                                $manuf = '';
                                            }
                                            $model = ($revidec >> 4) & 255;
                                            if (isset($raslist['model'][$model])) {
                                                $this->info->setMachine('Raspberry Pi ' . $raslist['model'][$model] . ' (PCB 1.' . ($revidec & 15) . $manuf . ')');
                                            } else {
                                                $this->info->setMachine('Raspberry Pi (PCB 1.' . ($revidec & 15) . $manuf . ')');
                                            }
                                        }
                                    } else {
                                        if ($this->info->getMachine() === '') {
                                            if (isset($raslist['old'][$revidec & 0x7fffff])) {
                                                $this->info->setMachine('Raspberry Pi ' . $raslist['old'][$revidec & 0x7fffff]);
                                            } else {
                                                $this->info->setMachine('Raspberry Pi');
                                            }
                                        }
                                    }
                                }
                            } elseif (($_hard !== null) && ($this->info->getMachine() === '')) { // other ARM hardware
                                $this->info->setMachine($_hard);
                            }
                            if ($cpulist === null) {
                                $cpulist = @parse_ini_file(DOCROOT . 'system/data/server/cpus.ini', true);
                            }
                            if ($cpulist && (isset($cpulist['cpu'][$cpuimplpart = strtolower($impl . ',' . $part)]))) {
                                if (($cpumodel = $dev->getModel()) !== '') {
                                    $dev->setModel($cpumodel . ' - ' . $cpulist['cpu'][$cpuimplpart]);
                                } else {
                                    $dev->setModel($cpulist['cpu'][$cpuimplpart]);
                                }
                            }
                        } elseif (($_hard !== null) && ($this->info->getMachine() === '')) { // other hardware
                            $this->info->setMachine($_hard);
                        }

                        if ($dev->getModel() === '') {
                            $dev->setModel('unknown');
                        }
                        $cpucount++;
                        $this->info->addCpus($dev);
                    }
                }
            }
            $cpudevices = glob('/sys/devices/system/cpu/cpu*/uevent', GLOB_NOSORT);
            if (is_array($cpudevices) && (($cpustopped = count($cpudevices) - $cpucount) > 0)) {
                for (; $cpustopped > 0; $cpustopped--) {
                    $dev = new CServer_Device_Cpu();
                    $dev->setModel('stopped');
                    if ($speedset) {
                        $dev->setCpuSpeed(-1);
                    }
                    $this->info->addCpus($dev);
                }
            }
        }
    }
}
