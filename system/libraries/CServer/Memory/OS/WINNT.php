<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Memory_OS_WINNT extends CServer_Memory_OS {
    /**
     * @return void
     */
    public function buildMemory() {
        $buf = '';
        if ($this->server->executeProgram('wmic', 'OS get TotalVisibleMemorySize,FreePhysicalMemory /value', $buf, false) && strlen(trim($buf)) > 0) {
            $this->parseWmicMemory($buf);
        } elseif ($this->server->executeProgram('systeminfo', '', $buf, false) && strlen(trim($buf)) > 0) {
            $this->parseSysteminfoMemory($buf);
        }
    }

    /**
     * @return void
     */
    public function buildSwap() {
        $buf = '';
        if ($this->server->executeProgram('wmic', 'pagefile get AllocatedBaseSize,CurrentUsage,Name /value', $buf, false) && strlen(trim($buf)) > 0) {
            $this->parseWmicSwap($buf);
        }
    }

    /**
     * @param string $buf
     *
     * @return void
     */
    protected function parseWmicMemory($buf) {
        $total = 0;
        $free = 0;
        if (preg_match('/FreePhysicalMemory=(\d+)/i', $buf, $m)) {
            $free = $m[1] * 1024;
        }
        if (preg_match('/TotalVisibleMemorySize=(\d+)/i', $buf, $m)) {
            $total = $m[1] * 1024;
        }
        if ($total > 0) {
            $this->info->setMemTotal($total);
            $this->info->setMemFree($free);
            $this->info->setMemUsed($total - $free);
        }
    }

    /**
     * @param string $buf
     *
     * @return void
     */
    protected function parseSysteminfoMemory($buf) {
        if (preg_match('/Total Physical Memory:\s+([\d\.,]+)\s*MB/i', $buf, $totalMatch)
            && preg_match('/Available Physical Memory:\s+([\d\.,]+)\s*MB/i', $buf, $freeMatch)
        ) {
            $total = (float) str_replace([',', '.'], '', $totalMatch[1]) * 1024 * 1024;
            $free = (float) str_replace([',', '.'], '', $freeMatch[1]) * 1024 * 1024;
            $this->info->setMemTotal($total);
            $this->info->setMemFree($free);
            $this->info->setMemUsed($total - $free);
        }
    }

    /**
     * @param string $buf
     *
     * @return void
     */
    protected function parseWmicSwap($buf) {
        $entries = preg_split('/\r?\n\r?\n/', trim($buf), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($entries as $entry) {
            $allocated = 0;
            $usage = 0;
            $name = 'SWAP';
            if (preg_match('/AllocatedBaseSize=(\d+)/i', $entry, $m)) {
                $allocated = $m[1] * 1024 * 1024;
            }
            if (preg_match('/CurrentUsage=(\d+)/i', $entry, $m)) {
                $usage = $m[1] * 1024 * 1024;
            }
            if (preg_match('/Name=(.+)/i', $entry, $m)) {
                $name = trim($m[1]);
            }
            if ($allocated > 0) {
                $dev = CServer_Factory::createDeviceDisk();
                $dev->setName('SWAP');
                $dev->setMountPoint($name);
                $dev->setFsType('swap');
                $dev->setTotal($allocated);
                $dev->setUsed($usage);
                $dev->setFree($allocated - $usage);
                $this->info->setSwapDevices($dev);
            }
        }
    }
}
