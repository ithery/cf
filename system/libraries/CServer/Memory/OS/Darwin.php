<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Memory_OS_Darwin extends CServer_Memory_OS_Linux {
    use CServer_Trait_OS_Darwin;

    /**
     * @return void
     */
    public function buildMemory() {
        $pstat = '';
        $s = $this->grabkey('hw.memsize');
        if ($this->server->executeProgram('vm_stat', '', $pstat, $this->server->config()->isDebug())) {
            if (preg_match('/^Pages free:\s+(\S+)/m', $pstat, $freeBuf)) {
                if (preg_match('/^Anonymous pages:\s+(\S+)/m', $pstat, $anonBuf) && preg_match('/^Pages wired down:\s+(\S+)/m', $pstat, $wireBuf) && preg_match('/^File-backed pages:\s+(\S+)/m', $pstat, $filebBuf)) {
                    $this->info->setMemFree($freeBuf[1] * 4 * 1024);
                    $this->info->setMemApplication(($anonBuf[1] + $wireBuf[1]) * 4 * 1024);
                    $this->info->setMemCache($filebBuf[1] * 4 * 1024);
                    if (preg_match('/^Pages occupied by compressor:\s+(\S+)/m', $pstat, $comprBuf)) {
                        $this->info->setMemBuffer($comprBuf[1] * 4 * 1024);
                    }
                } else {
                    if (preg_match('/^Pages speculative:\s+(\S+)/m', $pstat, $specBuf)) {
                        $this->info->setMemFree(($freeBuf[1] + $specBuf[1]) * 4 * 1024);
                    } else {
                        $this->info->setMemFree($freeBuf[1] * 4 * 1024);
                    }
                    $appMemory = 0;
                    if (preg_match('/^Pages wired down:\s+(\S+)/m', $pstat, $wireBuf)) {
                        $appMemory += $wireBuf[1] * 4 * 1024;
                    }
                    if (preg_match('/^Pages active:\s+(\S+)/m', $pstat, $activeBuf)) {
                        $appMemory += $activeBuf[1] * 4 * 1024;
                    }
                    $this->info->setMemApplication($appMemory);
                    if (preg_match('/^Pages inactive:\s+(\S+)/m', $pstat, $inactiveBuf)) {
                        $this->info->setMemCache($inactiveBuf[1] * 4 * 1024);
                    }
                }
            } else {
                $lines = preg_split("/\n/", $pstat, -1, PREG_SPLIT_NO_EMPTY);
                $parts = preg_split("/\s+/", $lines[1], 19);
                $this->info->setMemFree($parts[2] * 4 * 1024);
            }
            $this->info->setMemTotal($s);
            $this->info->setMemUsed($this->info->getMemTotal() - $this->info->getMemFree());
        }
    }

    /**
     * @return void
     */
    public function buildSwap() {
        $swapBuff = '';
        if ($this->server->executeProgram('sysctl', 'vm.swapusage | colrm 1 22', $swapBuff, $this->server->config()->isDebug())) {
            $swap1 = preg_split('/M/', $swapBuff);
            $swap2 = preg_split('/=/', $swap1[1]);
            $swap3 = preg_split('/=/', $swap1[2]);
            $dev = CServer_Factory::createDeviceDisk();
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
