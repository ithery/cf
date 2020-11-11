<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 7, 2019, 2:18:19 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Memory_OS_Darwin extends CServer_Memory_OS_Linux {

    use CServer_Trait_OS_Darwin;

    /**
     * get memory and swap information
     *
     * @return void
     */
    protected function buildMemory() {
        $cmd = $this->createCommand();
        $s = $this->grabkey('hw.memsize');
        if ($cmd->executeProgram('vm_stat', '', $pstat, PSI_DEBUG)) {
            // calculate free memory from page sizes (each page = 4096)
            if (preg_match('/^Pages free:\s+(\S+)/m', $pstat, $free_buf)) {
                if (preg_match('/^Anonymous pages:\s+(\S+)/m', $pstat, $anon_buf) && preg_match('/^Pages wired down:\s+(\S+)/m', $pstat, $wire_buf) && preg_match('/^File-backed pages:\s+(\S+)/m', $pstat, $fileb_buf)) {
                    // OS X 10.9 or never
                    $this->info->setMemFree($free_buf[1] * 4 * 1024);
                    $this->info->setMemApplication(($anon_buf[1] + $wire_buf[1]) * 4 * 1024);
                    $this->info->setMemCache($fileb_buf[1] * 4 * 1024);
                    if (preg_match('/^Pages occupied by compressor:\s+(\S+)/m', $pstat, $compr_buf)) {
                        $this->info->setMemBuffer($compr_buf[1] * 4 * 1024);
                    }
                } else {
                    if (preg_match('/^Pages speculative:\s+(\S+)/m', $pstat, $spec_buf)) {
                        $this->info->setMemFree(($free_buf[1] + $spec_buf[1]) * 4 * 1024);
                    } else {
                        $this->info->setMemFree($free_buf[1] * 4 * 1024);
                    }
                    $appMemory = 0;
                    if (preg_match('/^Pages wired down:\s+(\S+)/m', $pstat, $wire_buf)) {
                        $appMemory += $wire_buf[1] * 4 * 1024;
                    }
                    if (preg_match('/^Pages active:\s+(\S+)/m', $pstat, $active_buf)) {
                        $appMemory += $active_buf[1] * 4 * 1024;
                    }
                    $this->sys->setMemApplication($appMemory);
                    if (preg_match('/^Pages inactive:\s+(\S+)/m', $pstat, $inactive_buf)) {
                        $this->sys->setMemCache($inactive_buf[1] * 4 * 1024);
                    }
                }
            } else {
                $lines = preg_split("/\n/", $pstat, -1, PREG_SPLIT_NO_EMPTY);
                $ar_buf = preg_split("/\s+/", $lines[1], 19);
                $this->info->setMemFree($ar_buf[2] * 4 * 1024);
            }
            $this->info->setMemTotal($s);
            $this->info->setMemUsed($this->info->getMemTotal() - $this->info->getMemFree());
        }
    }

    public function buildSwap() {
        $cmd = $this->createCommand();
        if ($cmd->executeProgram('sysctl', 'vm.swapusage | colrm 1 22', $swapBuff, PSI_DEBUG)) {
            $swap1 = preg_split('/M/', $swapBuff);
            $swap2 = preg_split('/=/', $swap1[1]);
            $swap3 = preg_split('/=/', $swap1[2]);
            $dev = CServer::createDeviceDisk();
            $dev->setName('SWAP');
            $dev->setMountPoint('SWAP');
            $dev->setFsType('swap');
            $dev->setTotal($swap1[0] * 1024 * 1024);
            $dev->setUsed($swap2[1] * 1024 * 1024);
            $dev->setFree($swap3[1] * 1024 * 1024);
            $this->info->setSwapDevices($dev);
        }
    }

}
