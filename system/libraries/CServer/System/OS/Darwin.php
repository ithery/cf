<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 7, 2019, 2:18:36 PM
 */
class CServer_System_OS_Darwin extends CServer_System_OS_Linux {
    use CServer_Trait_OS_Darwin;

    /**
     * Processor Load
     * optionally create a loadbar
     *
     * @return void
     */
    protected function buildLoadAvg() {
        $s = $this->grabkey('vm.loadavg');
        $s = preg_replace('/{ /', '', $s);
        $s = preg_replace('/ }/', '', $s);
        $this->info->setLoad($s);
        if (CServer::config()->loadPercentEnabled() && (CServer::getOS() != 'Darwin')) {
            if ($fd = $this->grabkey('kern.cp_time')) {
                // Find out the CPU load
                // user + sys = load
                // total = total
                preg_match($this->_CPURegExp2, $fd, $res);
                $load = $res[2] + $res[3] + $res[4]; // cpu.user + cpu.sys
                $total = $res[2] + $res[3] + $res[4] + $res[5]; // cpu.total
                // we need a second value, wait 1 second befor getting (< 1 second no good value will occour)
                sleep(1);
                $fd = $this->grabkey('kern.cp_time');
                preg_match($this->_CPURegExp2, $fd, $res);
                $load2 = $res[2] + $res[3] + $res[4];
                $total2 = $res[2] + $res[3] + $res[4] + $res[5];
                $this->info->setLoadPercent((100 * ($load2 - $load)) / ($total2 - $total));
            }
        }
    }
}
