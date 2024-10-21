<?php

class CBase_Once_Cache implements Countable {
    public WeakMap $values;

    protected static self $cache;

    protected bool $enabled = true;

    /**
     * @return static
     */
    public static function instance() {
        return static::$cache ??= new static();
    }

    protected function __construct() {
        $this->values = new WeakMap();
    }

    public function has(object $object, string $backtraceHash): bool {
        if (!isset($this->values[$object])) {
            return false;
        }

        return array_key_exists($backtraceHash, $this->values[$object]);
    }

    /**
     * @param mixed  $object
     * @param string $backtraceHash
     *
     * @return mixed
     */
    public function get($object, string $backtraceHash) {
        return $this->values[$object][$backtraceHash];
    }

    /**
     * @param object $object
     * @param string $backtraceHash
     * @param mixed  $value
     *
     * @return void
     */
    public function set(object $object, string $backtraceHash, $value): void {
        $cached = $this->values[$object] ?? [];

        $cached[$backtraceHash] = $value;

        $this->values[$object] = $cached;
    }

    public function forget(object $object): void {
        unset($this->values[$object]);
    }

    public function flush(): self {
        $this->values = new WeakMap();

        return $this;
    }

    public function enable(): self {
        $this->enabled = true;

        return $this;
    }

    public function disable(): self {
        $this->enabled = false;

        return $this;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function count(): int {
        return count($this->values);
    }
}
