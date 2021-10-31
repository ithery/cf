<?php

/**
 * Handle serialization of floats.
 */
class CModel_Metable_DataType_Handler_FloatHandler extends CModel_Metable_DataType_Handler_ScalarHandler {
    /**
     * {@inheritdoc}
     */
    protected $type = 'double';

    /**
     * {@inheritdoc}
     */
    public function getDataType() {
        return 'float';
    }
}
