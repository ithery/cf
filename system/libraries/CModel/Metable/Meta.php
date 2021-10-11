<?php

/**
 * Model for storing meta data.
 *
 * @property int $meta_id
 * @property string $metable_type
 * @property int $metable_id
 * @property string $type
 * @property string $key
 * @property string $value
 * @property CModel $metable
 */
class CModel_Metable_Meta extends CModel {
    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $table = 'meta';

    /**
     * {@inheritdoc}
     */
    protected $guarded = ['id', 'metable_type', 'metable_id', 'type'];

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'type' => 'null',
        'value' => '',
    ];

    /**
     * Cache of unserialized value.
     *
     * @var mixed
     */
    protected $cachedValue;

    /**
     * Metable Relation.
     *
     * @return CModel_Relation_MorphTo
     */
    public function metable() {
        return $this->morphTo();
    }

    /**
     * Accessor for value.
     *
     * Will unserialize the value before returning it.
     *
     * Successive access will be loaded from cache.
     *
     * @return mixed
     *
     * @throws CModel_Metable_Exception_DataTypeException
     */
    public function getValueAttribute() {
        if (!isset($this->cachedValue)) {
            $this->cachedValue = $this->getDataTypeRegistry()
                ->getHandlerForType($this->type)
                ->unserializeValue($this->attributes['value']);
        }

        return $this->cachedValue;
    }

    /**
     * Mutator for value.
     *
     * The `type` attribute will be automatically updated to match the datatype of the input.
     *
     * @param mixed $value
     *
     * @throws CModel_Metable_Exception_DataTypeException
     */
    public function setValueAttribute($value): void {
        $registry = $this->getDataTypeRegistry();

        $this->attributes['type'] = $registry->getTypeForValue($value);
        $this->attributes['value'] = $registry->getHandlerForType($this->type)
            ->serializeValue($value);

        $this->cachedValue = null;
    }

    /**
     * Retrieve the underlying serialized value.
     *
     * @return string
     */
    public function getRawValue(): string {
        return $this->attributes['value'];
    }

    /**
     * Load the datatype Registry from the container.
     *
     * @return CModel_Metable_DataType_Registry
     */
    protected function getDataTypeRegistry() {
        return CModel_Metable_DataType_Registry::instance();
    }
}
