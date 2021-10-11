<?php

/**
 * Handle serialization of Serializable objects.
 */
class CModel_Metable_DataType_Handler_SerializableHandler implements CModel_Metable_DataType_HandlerInterface {
    /**
     * {@inheritdoc}
     */
    public function getDataType() {
        return 'serializable';
    }

    /**
     * {@inheritdoc}
     */
    public function canHandleValue($value) {
        return $value instanceof Serializable;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeValue($value) {
        return serialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function unserializeValue($value) {
        return unserialize($value);
    }
}
