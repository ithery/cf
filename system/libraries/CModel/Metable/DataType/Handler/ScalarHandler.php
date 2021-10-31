<?php

abstract class CModel_Metable_DataType_Handler_ScalarHandler implements CModel_Metable_DataType_HandlerInterface {
    /**
     * The name of the scalar data type.
     *
     * @var string
     */
    protected $type;

    /**
     * {@inheritdoc}
     */
    public function getDataType() {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandleValue($value) {
        return gettype($value) == $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeValue($value) {
        settype($value, 'string');

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function unserializeValue($value) {
        settype($value, $this->type);

        return $value;
    }
}
