<?php

interface CDebug_Contract_StorageInterface {
    /**
     * Saves collected data.
     *
     * @param string $id
     * @param string $data
     */
    public function save($id, $data);

    /**
     * Returns collected data with the specified id.
     *
     * @param string $id
     *
     * @return array
     */
    public function get($id);

    /**
     * Returns a metadata about collected data.
     *
     * @param array $filters
     * @param int   $max
     * @param int   $offset
     *
     * @return array
     */
    public function find(array $filters = [], $max = 20, $offset = 0);

    /**
     * Clears all the collected data.
     */
    public function clear();
}
