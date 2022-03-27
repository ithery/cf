<?php
class CRedis_DataType_Strings extends CRedis_DataTypeAbstract {
    /**
     * @inheritdoc
     */
    public function fetch($key) {
        return $this->getConnection()->get($key);
    }

    /**
     * @inheritdoc
     */
    public function update(array $params) {
        $this->store($params);
    }

    /**
     * @inheritdoc
     */
    public function store(array $params) {
        $key = carr::get($params, 'key');
        $value = carr::get($params, 'value');
        $seconds = carr::get($params, 'seconds');

        $this->getConnection()->set($key, $value);

        if ($seconds > 0) {
            $this->getConnection()->expire($key, $seconds);
        }
    }
}
