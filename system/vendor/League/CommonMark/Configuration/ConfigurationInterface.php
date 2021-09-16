<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Configuration;

interface ConfigurationInterface {
    /**
     * Merge an existing array into the current configuration
     *
     * @param array<string, mixed> $config
     */
    public function merge(array $config = []);

    /**
     * Replace the entire array with something else
     *
     * @param array<string, mixed> $config
     */
    public function replace(array $config = []);

    /**
     * Return the configuration value at the given key, or $default if no such config exists
     *
     * The key can be a string or a slash-delimited path to a nested value
     *
     * @param mixed|null $default
     * @param mixed      $key
     *
     * @return mixed|null
     */
    public function get($key, $default = null);

    /**
     * Set the configuration value at the given key
     *
     * The key can be a string or a slash-delimited path to a nested value
     *
     * @param mixed $value
     * @param mixed $key
     */
    public function set($key, $value);

    /**
     * Returns whether a configuration option exists at the given key
     *
     * @param mixed $key
     */
    public function exists($key);
}
