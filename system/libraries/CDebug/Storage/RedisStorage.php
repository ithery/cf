<?php

/**
 * Stores collected data into Redis.
 */
class CDebug_Storage_RedisStorage implements CDebug_Contract_StorageInterface {
    /**
     * @var \Predis\Client|\Redis
     */
    protected $redis;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @param \Predis\Client|\Redis $redis Redis Client
     * @param string                $hash
     */
    public function __construct($redis, $hash = 'phpdebugbar') {
        $this->redis = $redis;
        $this->hash = $hash;
    }

    /**
     * @inheritdoc
     */
    public function save($id, $data) {
        $this->redis->hSet("{$this->hash}:meta", $id, serialize($data['__meta']));
        unset($data['__meta']);
        $this->redis->hSet("{$this->hash}:data", $id, serialize($data));
    }

    /**
     * @inheritdoc
     */
    public function get($id) {
        return array_merge(
            unserialize($this->redis->hGet("{$this->hash}:data", $id)),
            ['__meta' => unserialize($this->redis->hGet("{$this->hash}:meta", $id))]
        );
    }

    /**
     * @inheritdoc
     */
    public function find(array $filters = [], $max = 20, $offset = 0) {
        $results = [];
        $cursor = '0';
        $isPhpRedis = get_class($this->redis) === 'Redis';

        do {
            if ($isPhpRedis) {
                $data = $this->redis->hScan("{$this->hash}:meta", $cursor);
            } else {
                list($cursor, $data) = $this->redis->hScan("{$this->hash}:meta", $cursor);
            }

            foreach ($data as $meta) {
                if ($meta = unserialize($meta)) {
                    if ($this->filter($meta, $filters)) {
                        $results[] = $meta;
                    }
                }
            }
        } while ($cursor);

        usort($results, static function ($a, $b) {
            return $b['utime'] <=> $a['utime'];
        });

        return array_slice($results, $offset, $max);
    }

    /**
     * Filter the metadata for matches.
     *
     * @param mixed $meta
     * @param mixed $filters
     */
    protected function filter($meta, $filters) {
        foreach ($filters as $key => $value) {
            if (!isset($meta[$key]) || fnmatch($value, $meta[$key]) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function clear() {
        $this->redis->del("{$this->hash}:data");
        $this->redis->del("{$this->hash}:meta");
    }
}
