<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CCache_DriverTaggableAbstract extends CCache_DriverAbstract implements CCache_DriverInterface {
    /**
     * Begin executing a new tags operation.
     *
     * @param array|mixed $names
     *
     * @return CCache_TaggedCache
     */
    public function tags($names) {
        return new CCache_TaggedCache($this, new CCache_TagSet($this, is_array($names) ? $names : func_get_args()));
    }
}
