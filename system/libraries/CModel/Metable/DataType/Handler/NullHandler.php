<?php

/**
 * Handle serialization of null values.
 */
class CModel_Metable_DataType_Handler_NullHandler extends CModel_Metable_DataType_Handler_ScalarHandler {
    /**
     * {@inheritdoc}
     */
    protected $type = 'NULL';

    /**
     * {@inheritdoc}
     */
    public function getDataType() {
        return 'null';
    }
}
