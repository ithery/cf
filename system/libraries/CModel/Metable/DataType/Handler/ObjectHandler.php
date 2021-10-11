<?php

/**
 * Handle serialization of plain objects.
 */
class CModel_Metable_DataType_Handler_ObjectHandler implements CModel_Metable_DataType_HandlerInterface {
    /**
     * {@inheritdoc}
     */
    public function getDataType() {
        return 'object';
    }

    /**
     * {@inheritdoc}
     */
    public function canHandleValue($value) {
        return is_object($value);
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
        return json_decode($value, false);
    }
}
