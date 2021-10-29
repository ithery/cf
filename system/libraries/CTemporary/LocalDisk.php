<?php

class CTemporary_LocalDisk extends CTemporary_DiskAbstract {
    public function __construct() {
        $this->disk = CStorage::instance()->createLocalDriver([
            'driver' => 'local',
            'root' => DOCROOT . 'temp',
            'url' => curl::httpbase() . 'temp',
            'visibility' => 'public',
        ]);
    }

    public function directory($folder) {
        return new CTemporary_Directory($this, $folder);
    }
}
