<?php

interface CCache_Contract_LockProviderDriverInterface {
    /**
     * Get a lock instance.
     *
     * @param string      $name
     * @param int         $seconds
     * @param null|string $owner
     *
     * @return \CCache_LockAbstract
     */
    public function lock($name, $seconds = 0, $owner = null);

    /**
     * Restore a lock instance using the owner identifier.
     *
     * @param string $name
     * @param string $owner
     *
     * @return \CCache_LockAbstract
     */
    public function restoreLock($name, $owner);
}
