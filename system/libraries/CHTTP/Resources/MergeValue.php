<?php

class CHTTP_Resources_MergeValue {
    /**
     * The data to be merged.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new merge value instance.
     *
     * @param \CCollection|\JsonSerializable|array $data
     *
     * @return void
     */
    public function __construct($data) {
        if ($data instanceof CCollection) {
            $this->data = $data->all();
        } elseif ($data instanceof JsonSerializable) {
            $this->data = $data->jsonSerialize();
        } else {
            $this->data = $data;
        }
    }
}
