<?php

/**
 * Handle serialization of arrays.
 */
class CModel_Metable_DataType_Handler_ArrayHandler implements CModel_Metable_DataType_HandlerInterface {
    /**
     * {@inheritdoc}
     */
    public function getDataType() {
        return 'array';
    }

    /**
     * {@inheritdoc}
     */
    public function canHandleValue($value) {
        return is_array($value);
    }

    /**
     * {@inheritdoc}
     */
    public function serializeValue($value) {
        return json_encode($value);
    }

    /**
     * {@inheritdoc}
     */
    public function unserializeValue($value) {
        return json_decode($value, true);
    }
}
