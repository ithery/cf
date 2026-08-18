<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Memory_OS_Linux extends CServer_Memory_OS {
    /**
     * @return void
     */
    public function buildMemory() {
        $mbuf = '';
        if ($this->server->rfts('/proc/meminfo', $mbuf)) {
            $lines = preg_split("/\n/", $mbuf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                if (preg_match('/^MemTotal:\s+(\d+)\s*kB/i', $line, $m)) {
                    $this->info->setMemTotal($m[1] * 1024);
                } elseif (preg_match('/^MemFree:\s+(\d+)\s*kB/i', $line, $m)) {
                    $this->info->setMemFree($m[1] * 1024);
                } elseif (preg_match('/^Cached:\s+(\d+)\s*kB/i', $line, $m)) {
                    $this->info->setMemCache($m[1] * 1024);
                } elseif (preg_match('/^Buffers:\s+(\d+)\s*kB/i', $line, $m)) {
                    $this->info->setMemBuffer($m[1] * 1024);
                }
            }
            $this->info->setMemUsed($this->info->getMemTotal() - $this->info->getMemFree());
            if ($this->info->getMemCache() !== null && $this->info->getMemBuffer() !== null) {
                $this->info->setMemApplication($this->info->getMemUsed() - $this->info->getMemCache() - $this->info->getMemBuffer());
            }
        }
    }

    /**
     * @return void
     */
    public function buildSwap() {
        $sbuf = '';
        if ($this->server->rfts('/proc/swaps', $sbuf, 0, 4096, false)) {
            $swaps = preg_split("/\n/", $sbuf, -1, PREG_SPLIT_NO_EMPTY);
            unset($swaps[0]);
            foreach ($swaps as $swap) {
                $parts = preg_split('/\s+/', $swap, 5);
                $dev = CServer_Factory::createDeviceDisk();
                $dev->setMountPoint($parts[0]);
                $dev->setName('SWAP');
                $dev->setTotal($parts[2] * 1024);
                $dev->setUsed($parts[3] * 1024);
                $dev->setFree($dev->getTotal() - $dev->getUsed());
                $this->info->setSwapDevices($dev);
            }
        }
    }
}
