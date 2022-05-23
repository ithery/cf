<?php

class CRedis_DataType_SortedSets extends CRedis_DataTypeAbstract {
    /**
     * @inheritdoc
     */
    public function fetch($key) {
        return $this->getConnection()->zrange($key, 0, -1, ['WITHSCORES' => true]);
    }

    /**
     * @inheritdoc
     */
    public function update(array $params) {
        $key = carr::get($params, 'key');
        $member = carr::get($params, 'member');
        $action = carr::get($params, 'action');

        if ($action === 'zrem') {
            $this->getConnection()->zrem($key, $member);
        }

        if ($action === 'zset') {
            $score = carr::get($params, 'score');
            $this->getConnection()->zadd($key, [$member => $score]);
        }
    }

    /**
     * @inheritdoc
     */
    public function store(array $params) {
        $key = carr::get($params, 'key');
        $members = carr::get($params, 'members');
        $expire = carr::get($params, 'expire');

        $fields = [];

        foreach ($members as $member) {
            $fields[$member['member']] = $member['score'];
        }

        $this->getConnection()->zadd($key, $fields);

        if ($expire > 0) {
            $this->getConnection()->expire($key, $expire);
        }
    }

    /**
     * Remove a member from a sorted set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params) {
        $key = carr::get($params, 'key');
        $member = carr::get($params, 'member');

        return $this->getConnection()->zrem($key, $member);
    }
}
