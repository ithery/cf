<?php

/*
 * This file is a part of dflydev/dot-access-data.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dflydev\DotAccessData;

use Dflydev\DotAccessData\Exception\DataException;
use Dflydev\DotAccessData\Exception\InvalidPathException;

interface DataInterface {
    const PRESERVE = 0;

    const REPLACE = 1;

    const MERGE = 2;

    /**
     * Append a value to a key (assumes key refers to an array value)
     *
     * If the key does not yet exist it will be created.
     * If the key references a non-array it's existing contents will be added into a new array before appending the new value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws InvalidPathException if the given key is empty
     */
    public function append($key, $value = null);

    /**
     * Set a value for a key
     *
     * If the key does not yet exist it will be created.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws InvalidPathException if the given key is empty
     * @throws DataException        if the given key does not target an array
     */
    public function set($key, $value = null);

    /**
     * Remove a key
     *
     * No exception will be thrown if the key does not exist
     *
     * @param string $key
     *
     * @throws InvalidPathException if the given key is empty
     */
    public function remove($key);

    /**
     * Get the raw value for a key
     *
     * If the key does not exist, an optional default value can be returned instead.
     * If no default is provided then an exception will be thrown instead.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     *
     * @throws InvalidPathException if the given key is empty
     * @throws InvalidPathException if the given key does not exist and no default value was given
     *
     * @psalm-mutation-free
     */
    public function get($key, $default = null);

    /**
     * Check if the key exists
     *
     * @param string $key
     *
     * @return bool
     *
     * @throws InvalidPathException if the given key is empty
     *
     * @psalm-mutation-free
     */
    public function has($key);

    /**
     * Get a data instance for a key
     *
     * @param string $key
     *
     * @return DataInterface
     *
     * @throws InvalidPathException if the given key is empty
     * @throws DataException        if the given key does not reference an array
     *
     * @psalm-mutation-free
     */
    public function getData($key);

    /**
     * Import data into existing data
     *
     * @param array<string, mixed>                     $data
     * @param self::PRESERVE|self::REPLACE|self::MERGE $mode
     */
    public function import(array $data, $mode = self::REPLACE);

    /**
     * Import data from an external data into existing data
     *
     * @param DataInterface                            $data
     * @param self::PRESERVE|self::REPLACE|self::MERGE $mode
     */
    public function importData(DataInterface $data, $mode = self::REPLACE);

    /**
     * Export data as raw data
     *
     * @return array<string, mixed>
     *
     * @psalm-mutation-free
     */
    public function export();
}
