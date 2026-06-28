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
     * @var CServer_Memory_OS
     */
    protected $os;

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
        $osName = $server->getOS();
        $osClass = 'CServer_Memory_OS_' . $osName;
        $this->os = new $osClass($server, $this->info);
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
    protected function ensureMemoryBuilt() {
        if (!$this->memoryBuilt) {
            $this->memoryBuilt = true;
            $this->os->buildMemory();
        }
    }

    /**
     * @return void
     */
    protected function ensureSwapBuilt() {
        if (!$this->swapBuilt) {
            $this->swapBuilt = true;
            $this->os->buildSwap();
        }
    }

    /**
     * @return int
     */
    public function getMemApplication() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemApplication();
    }

    /**
     * @return int
     */
    public function getMemFree() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemFree();
    }

    /**
     * @return int
     */
    public function getMemBuffer() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemBuffer();
    }

    /**
     * @return int
     */
    public function getMemTotal() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemTotal();
    }

    /**
     * @return int
     */
    public function getMemUsed() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemUsed();
    }

    /**
     * @return int
     */
    public function getMemCache() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemCache();
    }

    /**
     * @return float
     */
    public function getMemPercentUsed() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemPercentUsed();
    }

    /**
     * @return float
     */
    public function getMemPercentBuffer() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemPercentBuffer();
    }

    /**
     * @return float
     */
    public function getMemPercentApplication() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemPercentApplication();
    }

    /**
     * @return float
     */
    public function getMemPercentCache() {
        $this->ensureMemoryBuilt();

        return $this->info->getMemPercentCache();
    }

    /**
     * @return array
     */
    public function getSwapDevices() {
        $this->ensureSwapBuilt();

        return $this->info->getSwapDevices();
    }

    /**
     * @return int
     */
    public function getSwapFree() {
        $this->ensureSwapBuilt();

        return $this->info->getSwapFree();
    }

    /**
     * @return float
     */
    public function getSwapPercentUsed() {
        $this->ensureSwapBuilt();

        return $this->info->getSwapPercentUsed();
    }

    /**
     * @return int
     */
    public function getSwapTotal() {
        $this->ensureSwapBuilt();

        return $this->info->getSwapTotal();
    }

    /**
     * @return int
     */
    public function getSwapUsed() {
        $this->ensureSwapBuilt();

        return $this->info->getSwapUsed();
    }
}
