<?php
class CRedis_DataType_Hashes extends CRedis_DataTypeAbstract {
    /**
     * @inheritdoc
     */
    public function fetch($key) {
        return $this->getConnection()->hgetall($key);
    }

    /**
     * @inheritdoc
     */
    public function update(array $params) {
        $key = carr::get($params, 'key');

        if (carr::has($params, 'field')) {
            $field = carr::get($params, 'field');
            $value = carr::get($params, 'value');

            $this->getConnection()->hset($key, $field, $value);
        }

        if (carr::has($params, '_editable')) {
            $value = carr::get($params, 'value');
            $field = carr::get($params, 'pk');

            $this->getConnection()->hset($key, $field, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function store(array $params) {
        $key = carr::get($params, 'key');
        $seconds = carr::get($params, 'seconds');
        $dic = carr::get($params, 'dic');

        $fields = [];

        foreach ($dic as $item) {
            $fields[$item['name']] = $item['value'];
        }

        $this->getConnection()->hmset($key, $fields);

        if ($seconds > 0) {
            $this->getConnection()->expire($key, $seconds);
        }
    }

    /**
     * Remove a field from a hash.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params) {
        $key = carr::get($params, 'key');
        $field = carr::get($params, 'field');

        return $this->getConnection()->hdel($key, [$field]);
    }
}
