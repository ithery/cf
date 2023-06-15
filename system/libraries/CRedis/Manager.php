<?php

use Predis\Pipeline\Pipeline;
use Predis\Collection\Iterator\Keyspace;
use League\Flysystem\Cached\Storage\Predis;

class CRedis_Manager {
    /**
     * The callback that should be used to authenticate redis-manager users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * @var array
     */
    protected $dataType = [
        'string' => CRedis_DataType_Strings::class,
        'hash' => CRedis_DataType_Hashes::class,
        'set' => CRedis_DataType_Sets::class,
        'zset' => CRedis_DataType_SortedSets::class,
        'list' => CRedis_DataType_Lists::class,
    ];

    /**
     * @var CRedis_Manager
     */
    protected static $instance;

    /**
     * @var string
     */
    protected $connection;

    protected $disabledCommands = [];

    /**
     * Get instance of redis manager.
     *
     * @param string $connection
     *
     * @return CRedis_Manager
     */
    public static function instance($connection = 'default') {
        if (!is_array(static::$instance)) {
            static::$instance = [];
        }
        if (!isset(static::$instance[$connection])) {
            static::$instance[$connection] = new static($connection);
        }

        return static::$instance[$connection];
    }

    /**
     * RedisManager constructor.
     *
     * @param string $connection
     */
    public function __construct($connection = 'default') {
        $this->connection = $connection;
    }

    /**
     * @return CRedis_DataType_Lists
     */
    public function list() {
        return new CRedis_DataType_Lists($this->getConnection());
    }

    /**
     * @return CRedis_DataType_Strings
     */
    public function string() {
        return new CRedis_DataType_Strings($this->getConnection());
    }

    /**
     * @return CRedis_DataType_Hashes
     */
    public function hash() {
        return new CRedis_DataType_Hashes($this->getConnection());
    }

    /**
     * @return CRedis_DataType_Sets
     */
    public function set() {
        return new CRedis_DataType_Sets($this->getConnection());
    }

    /**
     * @return CRedis_DataType_SortedSets
     */
    public function zset() {
        return new CRedis_DataType_SortedSets($this->getConnection());
    }

    /**
     * Get connection collections.
     *
     * @return CCollection
     */
    public function getConnections() {
        return c::collect(CF::config('database.redis'))->filter(function ($conn) {
            return is_array($conn) && isset($conn['host']) && !empty($conn['host']);
        });
    }

    /**
     * Get a registered connection instance.
     *
     * @param string $connection
     *
     * @return CRedis_AbstractConnection
     */
    public function getConnection($connection = null) {
        if ($connection) {
            $this->connection = $connection;
        }

        $redis = CRedis::instance();
        $currentDriver = $redis->getDriver();
        $redis->setDriver('predis');
        $connection = $redis->resolve($this->connection);

        $redis->setDriver($currentDriver);

        return $connection;
    }

    /**
     * Get information of redis instance.
     *
     * @param mixed $section
     *
     * @return array
     */
    public function getInformation($section = null) {
        if ($section) {
            $info = $this->getConnection()->info($section);
            if ($section == 'commandstats') {
                return CRedis_Formatter_Information::commandstats($info);
            }

            return $info;
        }

        return $this->getConnection()->info();
    }

    /**
     * Scan keys in redis by giving pattern.
     *
     * @param string $pattern
     * @param int    $count
     *
     * @return array|\Predis\Pipeline\Pipeline|CCollection
     */
    public function scan($pattern = '*', $count = 100) {
        $client = $this->getConnection();
        $keys = [];

        $predisClient = $client->client();
        /** @var \Predis\Client $predisClient */
        $prefix = (string) $predisClient->getOptions()->prefix;
        foreach (new Keyspace($client->client(), $pattern) as $item) {
            $keys[] = $item;

            if (count($keys) == $count) {
                break;
            }
        }

        $script = <<<'LUA'
        local type = redis.call('type', KEYS[1])
        local ttl = redis.call('ttl', KEYS[1])
        return {KEYS[1], type, ttl}
LUA;

        // $result = $client->command('eval', [$script, 1, $keys[0]]);
        // cdbg::dd($keys[0], $result);
        $keys = $predisClient->pipeline(function (Pipeline $pipe) use ($keys, $script, $prefix) {
            foreach ($keys as $key) {
                if ($prefix && cstr::startsWith($key, $prefix) && strlen($key) > strlen($prefix)) {
                    $key = cstr::substr($key, strlen($prefix));
                }
                $key = '87712a67-d939-444c-ac14-c5c93fc59fc9';
                $pipe->eval($script, 1, $key);
            }
        });

        return $keys;
    }

    /**
     * Fetch value of a giving key.
     *
     * @param string $key
     *
     * @return array
     */
    public function fetch($key) {
        $client = $this->getConnection();
        $predisClient = $client->client();
        /** @var \Predis\Client $predisClient */
        $prefix = (string) $predisClient->getOptions()->prefix;
        if ($prefix && cstr::startsWith($key, $prefix) && strlen($key) > strlen($prefix)) {
            $key = cstr::substr($key, strlen($prefix));
        }
        if (!$client->exists($key)) {
            return [];
        }

        $type = $client->type($key)->__toString();

        /** @var DataType $class */
        $class = $this->{$type}();

        $value = $class->fetch($key);
        $expire = $class->ttl($key);

        return compact('key', 'value', 'expire', 'type');
    }

    /**
     * Update a specified key.
     *
     * @param CHTTP_Request $request
     *
     * @return bool
     */
    public function update(CHTTP_Request $request) {
        $key = $request->get('key');
        $type = $request->get('type');

        /** @var CRedis_DataTypeAbstract $class */
        $class = $this->{$type}();

        $class->update($request->all());

        $class->setTtl($key, $request->get('ttl'));
    }

    /**
     * Remove the specified key.
     *
     * @param array $keys
     *
     * @return int
     */
    public function del($keys) {
        return $this->getConnection()->del($keys);
    }

    /**
     * 运行redis命令.
     *
     * @param string $command
     * @param mixed  $db
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function execute($command, $db) {
        $command = explode(' ', trim($command));

        if ($this->commandDisabled($command[0])) {
            throw new \Exception("Command [{$command[0]}] is disabled!");
        }

        $client = $this->getConnection();

        if ($db !== null) {
            $client->select($db);
        }

        return $client->executeRaw($command);
    }

    /**
     * Determine if giving command is disabled.
     *
     * @param string $command
     *
     * @return bool
     */
    protected function commandDisabled($command) {
        $disabled = $this->disabledCommands;

        $disabled = array_map('strtoupper', (array) $disabled);

        return in_array(strtoupper($command), $disabled);
    }

    /**
     * @param $key
     * @param int $seconds
     *
     * @return int
     */
    public function expire($key, $seconds = -1) {
        if ($seconds > 0) {
            return $this->getConnection()->expire($key, $seconds);
        } else {
            return $this->getConnection()->persist($key);
        }
    }
}
