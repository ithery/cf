<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:49:34 PM
 * @licene Ittron Global Teknologi <ittron.co.id>
 */
interface CCache_Driver_FileDriver_EngineInterface {

    /**
     * Retrieve an content from the file by key.
     *
     * @param  string|array  $key
     * @param  bool          $lock
     * @return mixed
     */
    public function get($key, $lock = false);

    /**
     * get the content in the file by key
     *
     * @param  string  $key
     * @param  mixed   $content
     * @param  bool    $lock
     * @return void
     */
    public function put($key, $content, $lock = false);

    /**
     * check file exists by key
     *
     * @param  string  $key
     * @return void
     */
    public function exists($key);

    /**
     * delete file exists by key
     *
     * @param  string  $key
     * @return void
     */
    public function delete($key);
}
