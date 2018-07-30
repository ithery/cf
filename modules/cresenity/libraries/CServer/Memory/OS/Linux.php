<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 6:18:54 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Memory_OS_Linux extends CServer_Memory_OS {

    /**
     * Physical memory information and Swap Space information
     *
     * @return void
     */
    public function buildMemory() {
        $cmd = $this->createCommand();
        if ($cmd->rfts('/proc/meminfo', $mbuf)) {


            $bufe = preg_split("/\n/", $mbuf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($bufe as $buf) {
                if (preg_match('/^MemTotal:\s+(\d+)\s*kB/i', $buf, $ar_buf)) {
                    $this->info->setMemTotal($ar_buf[1] * 1024);
                } elseif (preg_match('/^MemFree:\s+(\d+)\s*kB/i', $buf, $ar_buf)) {
                    $this->info->setMemFree($ar_buf[1] * 1024);
                } elseif (preg_match('/^Cached:\s+(\d+)\s*kB/i', $buf, $ar_buf)) {
                    $this->info->setMemCache($ar_buf[1] * 1024);
                } elseif (preg_match('/^Buffers:\s+(\d+)\s*kB/i', $buf, $ar_buf)) {
                    $this->info->setMemBuffer($ar_buf[1] * 1024);
                }
            }
            $this->info->setMemUsed($this->info->getMemTotal() - $this->info->getMemFree());
            // values for splitting memory usage
            if ($this->info->getMemCache() !== null && $this->info->getMemBuffer() !== null) {
                $this->info->setMemApplication($this->info->getMemUsed() - $this->info->getMemCache() - $this->info->getMemBuffer());
            }
        }
    }

    public function buildSwap() {
        $cmd = $this->createCommand();
        if ($cmd->rfts('/proc/swaps', $sbuf, 0, 4096, false)) {
            $swaps = preg_split("/\n/", $sbuf, -1, PREG_SPLIT_NO_EMPTY);
            unset($swaps[0]);
            foreach ($swaps as $swap) {
                $ar_buf = preg_split('/\s+/', $swap, 5);
                $dev = CServer::createDeviceDisk();
                $dev->setMountPoint($ar_buf[0]);
                $dev->setName("SWAP");
                $dev->setTotal($ar_buf[2] * 1024);
                $dev->setUsed($ar_buf[3] * 1024);
                $dev->setFree($dev->getTotal() - $dev->getUsed());
                $this->info->setSwapDevices($dev);
            }
        }
    }

}
