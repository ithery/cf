<?php

use Beste\Json;

final class CVendor_Firebase_Messaging_RawMessageFromArray implements CVendor_Firebase_Messaging_MessageInterface {
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function jsonSerialize() {
        return Json::decode(Json::encode($this->data), true);
    }
}
