<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:49:34 PM
 */
interface CCache_Driver_FileDriver_EngineInterface {
    /**
     * Retrieve an content from the file by key.
     *
     * @param string|array $key
     * @param bool         $lock
     *
     * @return mixed
     */
    public function get($key, $lock = false);

    /**
     * Get the content in the file by key
     *
     * @param string $key
     * @param mixed  $content
     * @param bool   $lock
     *
     * @return void
     */
    public function put($key, $content, $lock = false);

    /**
     * Check file exists by key
     *
     * @param string $key
     *
     * @return void
     */
    public function exists($key);

    /**
     * Delete file exists by key
     *
     * @param string $key
     *
     * @return void
     */
    public function delete($key);

    /**
     * Delete all files
     */
    public function deleteDirectory();
}
