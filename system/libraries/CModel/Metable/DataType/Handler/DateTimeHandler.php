<?php

/**
 * Handle serialization of DateTimeInterface objects.
 */
class CModel_Metable_DataType_Handler_DateTimeHandler implements CModel_Metable_DataType_HandlerInterface {
    /**
     * The date format to use for serializing.
     *
     * @var string
     */
    protected $format = 'Y-m-d H:i:s.uO';

    /**
     * {@inheritdoc}
     */
    public function getDataType() {
        return 'datetime';
    }

    /**
     * {@inheritdoc}
     */
    public function canHandleValue($value) {
        return $value instanceof DateTimeInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeValue($value) {
        return $value->format($this->format);
    }

    /**
     * {@inheritdoc}
     */
    public function unserializeValue($value) {
        return CCarbon::createFromFormat($this->format, $value);
    }
}
