<?php

class CServer_Monitor_Cpu implements CServer_MonitorInterface {
    public function check(): string {
        return exec(" grep 'cpu ' /proc/stat | awk '{print ($2+$4)*100/($2+$4+$5)}' ");
    }
}
