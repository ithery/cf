<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Memory {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var CServer_Memory_Info
     */
    protected $info;

    /**
     * @var bool
     */
    protected $memoryBuilt = false;

    /**
     * @var bool
     */
    protected $swapBuilt = false;

    /**
     * @param CServer_Server $server
     */
    public function __construct(CServer_Server $server) {
        $this->server = $server;
        $this->info = new CServer_Memory_Info();
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @return void
     */
    protected function buildMemory() {
        if ($this->memoryBuilt) {
            return;
        }
        $this->memoryBuilt = true;

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
    protected function buildSwap() {
        if ($this->swapBuilt) {
            return;
        }
        $this->swapBuilt = true;

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

    /**
     * @return int
     */
    public function getMemApplication() {
        $this->buildMemory();

        return $this->info->getMemApplication();
    }

    /**
     * @return int
     */
    public function getMemFree() {
        $this->buildMemory();

        return $this->info->getMemFree();
    }

    /**
     * @return int
     */
    public function getMemBuffer() {
        $this->buildMemory();

        return $this->info->getMemBuffer();
    }

    /**
     * @return int
     */
    public function getMemTotal() {
        $this->buildMemory();

        return $this->info->getMemTotal();
    }

    /**
     * @return int
     */
    public function getMemUsed() {
        $this->buildMemory();

        return $this->info->getMemUsed();
    }

    /**
     * @return int
     */
    public function getMemCache() {
        $this->buildMemory();

        return $this->info->getMemCache();
    }

    /**
     * @return float
     */
    public function getMemPercentUsed() {
        $this->buildMemory();

        return $this->info->getMemPercentUsed();
    }

    /**
     * @return float
     */
    public function getMemPercentBuffer() {
        $this->buildMemory();

        return $this->info->getMemPercentBuffer();
    }

    /**
     * @return float
     */
    public function getMemPercentApplication() {
        $this->buildMemory();

        return $this->info->getMemPercentApplication();
    }

    /**
     * @return float
     */
    public function getMemPercentCache() {
        $this->buildMemory();

        return $this->info->getMemPercentCache();
    }

    /**
     * @return array
     */
    public function getSwapDevices() {
        $this->buildSwap();

        return $this->info->getSwapDevices();
    }

    /**
     * @return int
     */
    public function getSwapFree() {
        $this->buildSwap();

        return $this->info->getSwapFree();
    }

    /**
     * @return float
     */
    public function getSwapPercentUsed() {
        $this->buildSwap();

        return $this->info->getSwapPercentUsed();
    }

    /**
     * @return int
     */
    public function getSwapTotal() {
        $this->buildSwap();

        return $this->info->getSwapTotal();
    }

    /**
     * @return int
     */
    public function getSwapUsed() {
        $this->buildSwap();

        return $this->info->getSwapUsed();
    }
}
