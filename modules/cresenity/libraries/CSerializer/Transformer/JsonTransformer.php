<?php

/**
 * Class JsonTransformer.
 */
class CSerializer_Transformer_JsonTransformer extends CSerializer_Transformer_ArrayTransformer {
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value) {
        return \json_encode(
            parent::serialize($value),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
