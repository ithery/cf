<?php

trait CCache_Trait_HasCacheLockTrait {
    /**
     * Get a lock instance.
     *
     * @param string      $name
     * @param int         $seconds
     * @param null|string $owner
     *
     * @return \CCache_LockInterface
     */
    public function lock($name, $seconds = 0, $owner = null) {
        return new CCache_CacheLock($this, $name, $seconds, $owner);
    }

    /**
     * Restore a lock instance using the owner identifier.
     *
     * @param string $name
     * @param string $owner
     *
     * @return \CCache_LockInterface
     */
    public function restoreLock($name, $owner) {
        return $this->lock($name, 0, $owner);
    }
}
