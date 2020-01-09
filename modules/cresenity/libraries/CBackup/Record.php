<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Carbon\Carbon;

class CBackup_Record {

    /** @var \CStorage_Adapter */
    protected $disk;

    /** @var string */
    protected $path;

    /** @var bool */
    protected $exists;

    /** @var Carbon */
    protected $date;

    /** @var int */
    protected $size;

    public function __construct(CStorage_Adapter $disk, $path) {
        $this->disk = $disk;
        $this->path = $path;
    }

    public function disk() {
        return $this->disk;
    }

    public function path() {
        return $this->path;
    }

    public function exists() {
        if ($this->exists === null) {
            $this->exists = $this->disk->exists($this->path);
        }
        return $this->exists;
    }

    public function date() {
        if ($this->date === null) {
            $this->date = Carbon::createFromTimestamp($this->disk->lastModified($this->path));
        }
        return $this->date;
    }

    /**
     * Get the size in bytes.
     */
    public function size() {
        if ($this->size === null) {
            if (!$this->exists()) {
                return 0;
            }
            $this->size = $this->disk->size($this->path);
        }
        return $this->size;
    }

    public function stream() {
        return $this->disk->readStream($this->path);
    }

    public function delete() {
        $this->exists = null;
        $this->disk->delete($this->path);
        CBackup::output()->info("Deleted backup `{$this->path}`.");
    }

}
