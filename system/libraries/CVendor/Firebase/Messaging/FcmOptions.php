<?php

final class CVendor_Firebase_Messaging_FcmOptions implements JsonSerializable {
    /**
     * @var array<string, mixed>
     */
    private $data;

    /**
     * @param array<string, mixed> $data
     */
    private function __construct(array $data) {
        $this->data = $data;
    }

    public static function create() {
        return new self([]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data) {
        return new self($data);
    }

    public function withAnalyticsLabel($label) {
        $options = clone $this;
        $options->data['analytics_label'] = $label;

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() {
        return $this->data;
    }
}
