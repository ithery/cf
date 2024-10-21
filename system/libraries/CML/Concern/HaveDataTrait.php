<?php

trait CML_Concern_HaveDataTrait {
    protected $data;

    protected $ignoredAttributes = [];

    public function getData() {
        return $this->getDataArray();
    }

    protected function getDataArray() {
        return $this->mixedToArray($this->data);
    }

    private function mixedToArray($data, array $ignore_attrs = null) {
        if (is_array($data)) {
            $rv = $data;
        } elseif ($data instanceof CModel) {
            $rv = $data->toArray();
        } elseif ($data instanceof CCollection) {
            $rv = $data->toArray();
        } elseif ($data instanceof \iterable) {
            $rv = iterator_to_array($data);
        } else {
            throw new CML_Exception_GeneralException('Cannot convert this data type to array: ' . get_class($data));
        }
        $ignoredAttributes = $this->ignoredAttributes;
        if (!$ignoredAttributes) {
            foreach ($rv as &$v) {
                foreach ($ignoredAttributes as $i) {
                    if (isset($v[$i])) {
                        unset($v[$i]);
                    }
                }
            }
        }

        return $rv;
    }
}
