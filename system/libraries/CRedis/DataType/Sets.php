<?php
class CRedis_DataType_Sets extends CRedis_DataTypeAbstract {
    /**
     * @inheritdoc
     */
    public function fetch($key) {
        return $this->getConnection()->smembers($key);
    }

    /**
     * @inheritdoc
     */
    public function update(array $params) {
        $key = carr::get($params, 'key');
        $member = carr::get($params, 'member');
        $action = carr::get($params, 'action');

        if ($action === 'srem') {
            $this->getConnection()->srem($key, $member);
        }

        if ($action === 'sadd') {
            $this->getConnection()->sadd($key, [$member]);
        }
    }

    /**
     * @inheritdoc
     */
    public function store(array $params) {
        $key = carr::get($params, 'key');
        $members = carr::get($params, 'members');
        $seconds = carr::get($params, 'seconds');

        $this->getConnection()->sadd($key, $members);

        if ($seconds > 0) {
            $this->getConnection()->expire($key, $seconds);
        } else {
            $this->getConnection()->persist($key);
        }
    }

    /**
     * Remove a member from a set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params) {
        $key = carr::get($params, 'key');
        $member = carr::get($params, 'member');

        return $this->getConnection()->srem($key, $member);
    }
}
