<?php

class CRedis_DataType_Lists extends CRedis_DataTypeAbstract {
    /**
     * @inheritdoc
     */
    public function fetch($key) {
        return $this->getConnection()->lrange($key, 0, -1);
    }

    /**
     * @inheritdoc
     */
    public function update(array $params) {
        $key = carr::get($params, 'key');

        $action = carr::get($params, 'action');

        if (in_array($action, ['lpush', 'rpush'])) {
            $members = carr::get($params, 'members');
            $this->getConnection()->{$action}($key, $members);
        }

        if ($action == 'lset') {
            $value = carr::get($params, 'value');
            $index = carr::get($params, 'index');

            $this->getConnection()->lset($key, $index, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function store(array $params) {
        $key = carr::get($params, 'key');
        $members = carr::get($params, 'members');
        $expire = carr::get($params, 'expire');
        $action = carr::get($params, 'action', 'rpush');

        $members = array_column($members, 'value');

        $this->getConnection()->{$action}($key, $members);

        if ($expire > 0) {
            $this->getConnection()->expire($key, $expire);
        }
    }

    /**
     * Remove a member from list by index.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function remove(array $params) {
        $key = carr::get($params, 'key');
        $index = carr::get($params, 'index');

        $lua = <<<'LUA'
redis.call('lset', KEYS[1], ARGV[1], '__DELETED__');
redis.call('lrem', KEYS[1], 1, '__DELETED__');
LUA;

        return $this->getConnection()->eval($lua, 1, $key, $index);
    }
}
