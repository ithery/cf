<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 5:03:59 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CCache_DriverTaggableAbstract extends CCache_DriverAbstract implements CCache_DriverInterface {

    /**
     * Begin executing a new tags operation.
     *
     * @param  array|mixed  $names
     * @return CCache_TaggedCache
     */
    public function tags($names) {
        return new CCache_TaggedCache($this, new CCache_TagSet($this, is_array($names) ? $names : func_get_args()));
    }

}
