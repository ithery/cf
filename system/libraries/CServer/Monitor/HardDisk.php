<?php

class CServer_Monitor_Harddisk implements CServer_MonitorInterface {
    public function check(): string {
        return diskfreespace('/');
    }
}
