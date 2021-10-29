<?php

abstract class CTemporary_DiskAbstract {
    /**
     * @var CStorage_Adapter
     */
    protected $disk;

    /**
     * @var CStorage_Adapter
     */
    public function getDisk() {
        return $this->disk;
    }
}
