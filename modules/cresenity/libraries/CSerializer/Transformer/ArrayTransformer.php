<?php

class CSerializer_Transformer_ArrayTransformer extends CSerializer_TransformerAbstract {
    public function __construct() {
        //overwriting default constructor.
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize($value) {
        $this->recursiveSetValues($value);
        $this->recursiveUnset($value, [CSerializer_Serializer::CLASS_IDENTIFIER_KEY]);
        $this->recursiveFlattenOneElementObjectsToScalarType($value);

        return $value;
    }
}
