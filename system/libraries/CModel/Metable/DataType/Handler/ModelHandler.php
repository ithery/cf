<?php

/**
 * Handle serialization of Eloquent Models.
 */
class CModel_Metable_DataType_Handler_ModelHandler implements CModel_Metable_DataType_HandlerInterface {
    /**
     * {@inheritdoc}
     */
    public function getDataType() {
        return 'model';
    }

    /**
     * {@inheritdoc}
     */
    public function canHandleValue($value) {
        return $value instanceof CModel;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeValue($value) {
        if ($value->exists) {
            return get_class($value) . '#' . $value->getKey();
        }

        return get_class($value);
    }

    /**
     * {@inheritdoc}
     */
    public function unserializeValue($value) {
        // Return blank instances.
        if (strpos($value, '#') === false) {
            return new $value();
        }

        // Fetch specific instances.
        list($class, $id) = explode('#', $value);

        return c::with(new $class())->findOrFail($id);
    }
}
