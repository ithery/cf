<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Configuration;

final class Configuration implements ConfigurationInterface {
    /**
     * @internal
     */
    const MISSING = '833f2700-af8d-49d4-9171-4b5f12d3bfbc';

    /**
     * @var array<string, mixed>
     */
    private $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = []) {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $config = []) {
        $this->config = \array_replace_recursive($this->config, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $config = []) {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null) {
        // accept a/b/c as ['a']['b']['c']
        if (\strpos($key, '/')) {
            return $this->getConfigByPath($key, $default);
        }

        if (!isset($this->config[$key])) {
            return $default;
        }

        return $this->config[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value) {
        // accept a/b/c as ['a']['b']['c']
        if (\strpos($key, '/')) {
            $this->setByPath($key, $value);
        }

        $this->config[$key] = $value;
    }

    public function exists($key) {
        return $this->getConfigByPath($key, self::MISSING) !== self::MISSING;
    }

    /**
     * @param mixed|null $default
     * @param mixed      $keyPath
     *
     * @return mixed|null
     */
    private function getConfigByPath($keyPath, $default = null) {
        $keyArr = \explode('/', $keyPath);
        $data = $this->config;
        foreach ($keyArr as $k) {
            if (!\is_array($data) || !isset($data[$k])) {
                return $default;
            }

            $data = $data[$k];
        }

        return $data;
    }

    /**
     * @param mixed|null $value
     * @param mixed      $keyPath
     */
    private function setByPath($keyPath, $value = null) {
        $keyArr = \explode('/', $keyPath);
        $pointer = &$this->config;
        while (($k = \array_shift($keyArr)) !== null) {
            if (!\is_array($pointer)) {
                $pointer = [];
            }

            if (!isset($pointer[$k])) {
                $pointer[$k] = null;
            }

            $pointer = &$pointer[$k];
        }

        $pointer = $value;
    }
}
