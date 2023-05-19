<?php

class CServer_Monitor_Memory implements CServer_MonitorInterface {
    public function check(): string {
        return exec(" free | grep Mem | awk '{print $3/$2 * 100.0}' ");
    }
}
