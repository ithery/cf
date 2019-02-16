<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:49:34 PM
 * @licene Ittron Global Teknologi <ittron.co.id>
 */
interface CCache_Driver_FileDriver_EngineInterface {

    /**
     * Get the full path of the given key
     *
     * @param string $key
     * @return string
     */
    public function path($key);

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @param  bool  $lock
     * @return string
     *
     */
    public function get($path, $lock = false);

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  bool  $lock
     * @return int|bool
     */
    public function put($path, $contents, $lock = false);
}
