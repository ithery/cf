<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_System_OS_Darwin extends CServer_System_OS_Linux {
    use CServer_Trait_OS_Darwin;

    /**
     * @return void
     */
    public function buildLoadAvg() {
        $s = $this->grabkey('vm.loadavg');
        $s = preg_replace('/{ /', '', $s);
        $s = preg_replace('/ }/', '', $s);
        $this->info->setLoad($s);
    }
}
