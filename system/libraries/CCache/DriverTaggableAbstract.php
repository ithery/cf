<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 5:03:59 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CCache_DriverTaggableAbstract implements CCache_DriverInterface {

    /**
     * Begin executing a new tags operation.
     *
     * @param  array|mixed  $names
     * @return \Illuminate\Cache\TaggedCache
     */
    public function tags($names) {
        return new TaggedCache($this, new TagSet($this, is_array($names) ? $names : func_get_args()));
    }

}
