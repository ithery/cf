<?php

class CSerializer_Transformer_FlatArrayTransformer extends CSerializer_Transformer_ArrayTransformer {
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value) {
        return $this->flatten(parent::serialize($value));
    }

    /**
     * @param array  $array
     * @param string $prefix
     *
     * @return array
     */
    private function flatten(array $array, $prefix = '') {
        $result = [];
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $result = $result + $this->flatten($value, $prefix . $key . '.');
            } else {
                $result[$prefix . $key] = $value;
            }
        }

        return $result;
    }
}
