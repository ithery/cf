<?php

class CCache_Lock_FileLock extends CCache_CacheLock {
    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire() {
        return $this->driver->add($this->name, $this->owner, $this->seconds);
    }
}
