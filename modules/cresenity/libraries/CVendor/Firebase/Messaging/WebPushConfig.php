<?php

/**
 * @see https://tools.ietf.org/html/rfc8030#section-5.3 Web Push Message Urgency
 */
final class CVendor_Firebase_Messaging_WebPushConfig implements JsonSerializable {
    const URGENCY_VERY_LOW = 'very-low';

    const URGENCY_LOW = 'low';

    const URGENCY_NORMAL = 'normal';

    const URGENCY_HIGH = 'high';

    /**
     * @var array<string, mixed>
     */
    private $rawConfig = [];

    private function __construct() {
    }

    public static function new() {
        return self::fromArray([]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data) {
        $config = new self();
        $config->rawConfig = $data;

        return $config;
    }

    public function withHighUrgency() {
        return $this->withUrgency(self::URGENCY_HIGH);
    }

    public function withNormalUrgency() {
        return $this->withUrgency(self::URGENCY_NORMAL);
    }

    public function withLowUrgency() {
        return $this->withUrgency(self::URGENCY_LOW);
    }

    public function withVeryLowUrgency() {
        return $this->withUrgency(self::URGENCY_VERY_LOW);
    }

    public function withUrgency($urgency) {
        $config = clone $this;
        $config->rawConfig['headers'] = isset($config->rawConfig['headers']) ? $config->rawConfig['headers'] : [];
        $config->rawConfig['headers']['Urgency'] = $urgency;

        return $config;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() {
        return \array_filter($this->rawConfig, static function ($value) {
            return $value !== null;
        });
    }
}
